<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore\Contracts;

/**
 * Interface describing the contract for managing semaphore locks.
 */
interface SemaphoreProviderInterface
{
    public function obtainLock(
        string $lockId,
        ?float $concurrentCheckEvery = null,
        int    $concurrentTimeout = 0
    ): SemaphoreLockInterface;
}