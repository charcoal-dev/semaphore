<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore\Filesystem;

use Charcoal\Filesystem\Path\DirectoryPath;
use Charcoal\Semaphore\Contracts\SemaphoreProviderInterface;
use Charcoal\Semaphore\Exceptions\SemaphoreException;
use Charcoal\Semaphore\Exceptions\SemaphoreLockException;

/**
 * Class FilesystemSemaphore
 * @package Charcoal\Semaphore
 */
readonly class FilesystemSemaphore implements SemaphoreProviderInterface
{
    /**
     * @param DirectoryPath $directory
     * @throws SemaphoreException
     */
    public function __construct(public DirectoryPath $directory)
    {
        $permissions = DIRECTORY_SEPARATOR === "\\" ? $directory->writable
            : ($directory->writable && $directory->executable);
        if (!$permissions) {
            throw new SemaphoreException(
                sprintf('Semaphore lacks required directory perms for file locks: "%s/"',
                    basename($directory->absolute)));
        }
    }

    /**
     * @param string $lockId
     * @param float|null $concurrentCheckEvery
     * @param int $concurrentTimeout
     * @return FileLock
     * @throws SemaphoreLockException
     */
    public function obtainLock(
        string $lockId,
        ?float $concurrentCheckEvery = null,
        int    $concurrentTimeout = 0
    ): FileLock
    {
        return new FileLock($this, $lockId, $concurrentCheckEvery, $concurrentTimeout);
    }
}
