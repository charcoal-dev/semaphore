<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore\Exception;

/**
 * Class SemaphoreLockError
 * @package Charcoal\Semaphore\Exception
 */
enum SemaphoreLockError: int
{
    case LOCK_OBTAIN_ERROR = 100;
    case CONCURRENT_REQUEST_BLOCKED = 200;
    case CONCURRENT_REQUEST_TIMEOUT = 300;
    case LOCK_RELEASE_ERROR = 1100;
}
