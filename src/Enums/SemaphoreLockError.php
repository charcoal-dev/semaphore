<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore\Enums;

/**
 * This enum provides integer-based error codes for various lock-related scenarios,
 * helping to differentiate between specific errors during semaphore lock handling.
 */
enum SemaphoreLockError: int
{
    case LOCK_OBTAIN_ERROR = 100;
    case CONCURRENT_REQUEST_BLOCKED = 200;
    case CONCURRENT_REQUEST_TIMEOUT = 300;
}
