<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore;

/**
 * Class AbstractSemaphore
 * @package Charcoal\Semaphore
 */
abstract class AbstractSemaphore
{
    abstract public function obtainLock(
        string $resourceId,
        ?float $concurrentCheckEvery = null,
        int    $concurrentTimeout = 0
    ): AbstractLock;
}
