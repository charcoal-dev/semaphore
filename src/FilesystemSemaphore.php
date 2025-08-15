<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore;

use Charcoal\Filesystem\Directory;
use Charcoal\Semaphore\Exceptions\SemaphoreException;
use Charcoal\Semaphore\Filesystem\FileLock;

/**
 * Class FilesystemSemaphore
 * @package Charcoal\Semaphore
 */
class FilesystemSemaphore extends AbstractSemaphore
{
    public readonly string $directory;

    /**
     * @param Directory $directory
     * @throws SemaphoreException
     * @throws \Charcoal\Filesystem\Exceptions\FilesystemException
     */
    public function __construct(Directory $directory)
    {
        if (!$directory->isReadable()) {
            throw new SemaphoreException('Semaphore locks directory is not readable');
        } elseif (!$directory->isWritable()) {
            throw new SemaphoreException('Semaphore locks directory is not writable');
        }

        $this->directory = $directory->path;
    }

    /**
     * @param string $resourceId
     * @param float|null $concurrentCheckEvery
     * @param int $concurrentTimeout
     * @return FileLock
     * @throws Exceptions\SemaphoreLockException
     */
    public function obtainLock(
        string $resourceId,
        ?float $concurrentCheckEvery = null,
        int    $concurrentTimeout = 0
    ): FileLock
    {
        return new FileLock($this, $resourceId, $concurrentCheckEvery, $concurrentTimeout);
    }
}
