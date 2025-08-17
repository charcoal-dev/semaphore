<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore\Filesystem;

use Charcoal\Base\Support\ErrorHelper;
use Charcoal\Base\Traits\NoDumpTrait;
use Charcoal\Base\Traits\NotCloneableTrait;
use Charcoal\Base\Traits\NotSerializableTrait;
use Charcoal\Semaphore\Contracts\SemaphoreLockInterface;
use Charcoal\Semaphore\Enums\SemaphoreLockError;
use Charcoal\Semaphore\Exceptions\SemaphoreLockException;
use Charcoal\Semaphore\Exceptions\SemaphoreUnlockException;

/**
 * Class FileLock
 * @package Charcoal\Semaphore\Filesystem
 */
class FileLock implements SemaphoreLockInterface
{
    public readonly string $lockFilepath;
    public bool $deleteFileOnRelease = false;

    protected ?float $previousTimestamp;
    protected bool $isLocked = false;
    protected bool $autoReleaseSet = false;

    use NotSerializableTrait;
    use NotCloneableTrait;
    use NoDumpTrait;

    /** @var mixed|resource File-pointer resource or NULL */
    private mixed $fp;

    /**
     * @param FilesystemSemaphore $provider
     * @param string $lockId must match regex: /^\w+$/
     * @param float|null $concurrentCheckEvery
     * @param int $concurrentTimeout
     * @throws SemaphoreLockException
     */
    public function __construct(
        public readonly FilesystemSemaphore $provider,
        public readonly string              $lockId,
        public readonly ?float              $concurrentCheckEvery = null,
        public readonly int                 $concurrentTimeout = 0,
    )
    {
        if (!preg_match('/^\w+$/', $lockId)) {
            throw new \InvalidArgumentException('Invalid resource identifier for semaphore emulator');
        }

        $this->lockFilepath = $this->provider->directory->absolute . DIRECTORY_SEPARATOR .
            $lockId . ".lock";

        error_clear_last();
        $fp = @fopen($this->lockFilepath, "c+");
        if (!$fp) {
            throw new SemaphoreLockException(
                SemaphoreLockError::LOCK_OBTAIN_ERROR,
                "Cannot get pointer resource to lock file",
                captureLastError: true
            );
        }

        $concurrentSleep = $concurrentCheckEvery && $concurrentCheckEvery > 0 ?
            (int)($concurrentCheckEvery * 1_000_000) : null;

        $timer = microtime(true);
        while (true) {
            if (!flock($fp, LOCK_EX | LOCK_NB)) {
                if (!$concurrentSleep) {
                    fclose($fp);
                    throw new SemaphoreLockException(SemaphoreLockError::CONCURRENT_REQUEST_BLOCKED);
                }

                usleep($concurrentSleep);
                if ($concurrentTimeout > 0) {
                    if ((microtime(true) - $timer) >= $concurrentTimeout) {
                        fclose($fp);
                        throw new SemaphoreLockException(SemaphoreLockError::CONCURRENT_REQUEST_TIMEOUT);
                    }
                }

                continue;
            }

            break;
        }

        $previousTimestamp = fread($fp, 32);
        if ($previousTimestamp) {
            $this->previousTimestamp = floatval($previousTimestamp);
        }

        ftruncate($fp, 0);
        fseek($fp, 0, SEEK_SET);
        @fwrite($fp, strval(microtime(true)));
        fflush($fp);

        if ($error = ErrorHelper::lastErrorToRuntimeException()) {
            fclose($fp);
            throw new SemaphoreLockException(
                SemaphoreLockError::LOCK_OBTAIN_ERROR,
                "Filesystem error: " . $error->getMessage(),
                previous: $error
            );
        }

        $this->isLocked = true;
        $this->fp = $fp;
    }

    /**
     * @return void
     * @throws SemaphoreUnlockException
     */
    public function releaseLock(): void
    {
        if (!$this->isLocked || !$this->fp) {
            return;
        }

        $this->isLocked = false;
        $unlock = flock($this->fp, LOCK_UN);
        fclose($this->fp);
        $this->fp = null;

        if (!$unlock) {
            throw new SemaphoreUnlockException("flock() failed");
        }

        if ($this->deleteFileOnRelease) {
            if (!@unlink($this->lockFilepath)) {
                throw new SemaphoreUnlockException(
                    "Failed to delete lock file",
                    captureLastError: true
                );
            }
        }
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    /**
     * @return void
     * @throws SemaphoreUnlockException
     */
    public function setAutoRelease(): void
    {
        if ($this->autoReleaseSet) {
            return;

        }

        $resourceLock = $this;
        register_shutdown_function(function () use ($resourceLock) {
            $resourceLock->releaseLock();
        });

        $this->autoReleaseSet = true;
    }

    /**
     * @return float|null
     */
    public function previousTimestamp(): ?float
    {
        return $this->previousTimestamp;
    }

    /**
     * @param float $seconds
     * @return bool
     */
    public function checkElapsedTime(float $seconds): bool
    {
        if (!$this->previousTimestamp) {
            return true;
        }

        return ((microtime(true) - $this->previousTimestamp) >= $seconds);
    }
}
