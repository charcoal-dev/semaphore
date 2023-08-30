<?php
/*
 * This file is a part of "charcoal-dev/semaphore" package.
 * https://github.com/charcoal-dev/semaphore
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/charcoal-dev/semaphore/blob/main/LICENSE
 */

declare(strict_types=1);

namespace Charcoal\Semaphore;

use Charcoal\Filesystem\Directory;
use Charcoal\Semaphore\Exception\SemaphoreException;
use Charcoal\Semaphore\Filesystem\FileLock;

/**
 * Class FilesystemSemaphore
 * @package Charcoal\Semaphore
 */
class FilesystemSemaphore extends AbstractSemaphore
{
    public readonly string $directory;

    /**
     * @param \Charcoal\Filesystem\Directory $directory
     * @throws \Charcoal\Filesystem\Exception\FilesystemException
     * @throws \Charcoal\Semaphore\Exception\SemaphoreException
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
     * @return \Charcoal\Semaphore\Filesystem\FileLock
     * @throws \Charcoal\Semaphore\Exception\SemaphoreLockException
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
