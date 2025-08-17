<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore\Contracts;

/**
 * Interface SemaphoreLockInterface
 * @package Charcoal\Semaphore\Contracts
 * @property-read string $lockId
 */
interface SemaphoreLockInterface
{
    public function releaseLock(): void;

    public function isLocked(): bool;

    public function setAutoRelease(): void;

    public function previousTimestamp(): ?float;

    public function checkElapsedTime(float $seconds): bool;
}