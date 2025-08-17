<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore;

use Charcoal\Base\Support\Helpers\ObjectHelper;
use Charcoal\Filesystem\Enums\PathType;
use Charcoal\Filesystem\Node\PathInfo;
use Charcoal\Semaphore\Contracts\SemaphoreProviderInterface;
use Charcoal\Semaphore\Exceptions\SemaphoreException;
use Charcoal\Semaphore\Filesystem\FileLock;

/**
 * Class FilesystemSemaphore
 * @package Charcoal\Semaphore
 */
readonly class FilesystemSemaphore implements SemaphoreProviderInterface
{
    /**
     * @param PathInfo $directory
     * @throws SemaphoreException
     */
    public function __construct(public PathInfo $directory)
    {
        if ($directory->type !== PathType::Directory) {
            throw new SemaphoreException(ObjectHelper::baseClassName(static::class) .
                " expects a directory path, got " . $directory->type->name);
        }

        $permissions = DIRECTORY_SEPARATOR === "\\" ? $directory->writable
            : ($directory->writable && $directory->executable);
        if (!$permissions) {
            throw new SemaphoreException(
                sprintf('Semaphore lacks required directory perms for file locks: "%s/"',
                    $directory->basename));
        }
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
