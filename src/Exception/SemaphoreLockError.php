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
