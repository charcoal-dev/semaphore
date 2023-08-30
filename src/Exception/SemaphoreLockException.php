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
 * Class SemaphoreLockException
 * @package Charcoal\Semaphore\Exception
 */
class SemaphoreLockException extends SemaphoreException
{
    /**
     * @param \Charcoal\Semaphore\Exception\SemaphoreLockError $error
     * @param string $message
     * @param \Throwable|null $previous
     */
    public function __construct(
        public readonly SemaphoreLockError $error,
        string                             $message = "",
        ?\Throwable                        $previous = null)
    {
        parent::__construct($message, $this->error->value, $previous);
    }
}
