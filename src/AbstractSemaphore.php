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

/**
 * Class AbstractSemaphore
 * @package Charcoal\Semaphore
 */
abstract class AbstractSemaphore
{
    /**
     * @param string $resourceId
     * @param float|null $concurrentCheckEvery
     * @param int $concurrentTimeout
     * @return mixed
     */
    abstract public function obtainLock(
        string $resourceId,
        ?float $concurrentCheckEvery = null,
        int    $concurrentTimeout = 0
    ): AbstractLock;
}
