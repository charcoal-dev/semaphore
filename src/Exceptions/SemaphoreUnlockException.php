<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore\Exceptions;

/**
 * This exception is typically used to signal errors related to releasing
 * or unlocking semaphores in concurrent or parallel processing contexts.
 */
class SemaphoreUnlockException extends SemaphoreException
{
    public function __construct(
        string      $message = "",
        int         $code = 0,
        ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}