<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore\Filesystem;

use Charcoal\Semaphore\AbstractLock;
use Charcoal\Semaphore\Exception\SemaphoreLockError;
use Charcoal\Semaphore\Exception\SemaphoreLockException;
use Charcoal\Semaphore\FilesystemSemaphore;

/**
 * Class FileLock
 * @package Charcoal\Semaphore\Filesystem
 */
class FileLock extends AbstractLock
{
    public readonly string $lockFilepath;
    public bool $deleteFileOnRelease = false;

    /** @var mixed|resource File-pointer resource or NULL */
    private mixed $fp;

    /**
     * @param FilesystemSemaphore $semaphore
     * @param string $resourceId
     * @param float|null $concurrentCheckEvery
     * @param int $concurrentTimeout
     * @throws SemaphoreLockException
     */
    public function __construct(
        FilesystemSemaphore $semaphore,
        string              $resourceId,
        ?float              $concurrentCheckEvery = null,
        int                 $concurrentTimeout = 0
    )
    {
        parent::__construct($semaphore, $resourceId, $concurrentCheckEvery, $concurrentTimeout);
        if (!preg_match('/^\w+$/', $resourceId)) {
            throw new \InvalidArgumentException('Invalid resource identifier for semaphore emulator');
        }

        $this->lockFilepath = $this->semaphore->directory . DIRECTORY_SEPARATOR . $resourceId . ".lock";
        $fp = fopen($this->lockFilepath, "c+");
        if (!$fp) {
            throw new SemaphoreLockException(
                SemaphoreLockError::LOCK_OBTAIN_ERROR,
                "Cannot get pointer resource to lock file"
            );
        }

        $concurrentSleep = $concurrentCheckEvery && $concurrentCheckEvery > 0 ?
            (int)($concurrentCheckEvery * 1_000_000) : null;

        $timer = time();
        while (true) {
            if (!flock($fp, LOCK_EX | LOCK_NB)) {
                if (!$concurrentSleep) {
                    throw new SemaphoreLockException(SemaphoreLockError::CONCURRENT_REQUEST_BLOCKED);
                }

                usleep($concurrentSleep);
                if ($concurrentTimeout > 0) {
                    if ((time() - $timer) >= $concurrentTimeout) {
                        throw new SemaphoreLockException(SemaphoreLockError::CONCURRENT_REQUEST_TIMEOUT);
                    }
                }

                continue;
            }

            break;
        }

        $previousTimestamp = fread($fp, 15);
        if ($previousTimestamp) {
            $this->previousTimestamp = floatval($previousTimestamp);
        }

        ftruncate($fp, 0);
        fseek($fp, 0, SEEK_SET);
        fwrite($fp, strval(microtime(true)));
        $this->isLocked = true;
        $this->fp = $fp;
    }

    /**
     * @return void
     * @throws SemaphoreLockException
     */
    public function releaseLock(): void
    {
        if (!$this->isLocked || !$this->fp) {
            return;
        }

        $unlock = flock($this->fp, LOCK_UN);
        if (!$unlock) {
            throw new SemaphoreLockException(SemaphoreLockError::LOCK_RELEASE_ERROR);
        }

        $this->isLocked = false;
        fclose($this->fp);
        $this->fp = null;

        if ($this->deleteFileOnRelease) {
            unlink($this->lockFilepath);
        }
    }
}
