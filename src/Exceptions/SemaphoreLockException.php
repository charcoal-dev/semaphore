<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore\Exceptions;

use Charcoal\Semaphore\Enums\SemaphoreLockError;

/**
 * Represents an exception thrown when a semaphore lock encounters an error.
 */
class SemaphoreLockException extends SemaphoreException
{
    public function __construct(
        public readonly SemaphoreLockError $error,
        string                             $message = "",
        ?\Throwable                        $previous = null)
    {
        parent::__construct($message ?: $this->error->name, $this->error->value, $previous);
    }
}
