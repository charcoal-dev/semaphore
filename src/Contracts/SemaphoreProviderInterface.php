<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore\Contracts;

use Charcoal\Semaphore\Exceptions\SemaphoreLockException;

/**
 * Interface describing the contract for managing semaphore locks.
 */
interface SemaphoreProviderInterface
{
    /**
     * @throws SemaphoreLockException
     */
    public function obtainLock(
        string  $lockId,
        ?float  $concurrentCheckEvery = null,
        int     $concurrentTimeout = 0,
        ?string $namespace = null
    ): SemaphoreLockInterface;
}