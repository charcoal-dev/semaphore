<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore\Contracts;

/**
 * Provides an interface for managing semaphore locks with unique identifiers
 * and additional functionality for lock state checks, time management, and auto-release behavior.
 */
interface SemaphoreLockInterface
{
    public function lockId(): string;

    public function releaseLock(): void;

    public function isLocked(): bool;

    public function setAutoRelease(): void;

    public function previousTimestamp(): ?float;

    public function checkElapsedTime(float $seconds): bool;
}