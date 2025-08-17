<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore\Exceptions;

use Charcoal\Base\Support\ErrorHelper;

/**
 * Class SemaphoreLockException
 * @package Charcoal\Semaphore\Exceptions
 */
class SemaphoreLockException extends SemaphoreException
{
    public function __construct(
        public readonly SemaphoreLockError $error,
        string                             $message = "",
        bool                               $captureLastError = false,
        ?\Throwable                        $previous = null)
    {
        parent::__construct($message ?: $this->error->name, $this->error->value, previous: $captureLastError ?
            ErrorHelper::lastErrorToRuntimeException() : $previous);
    }
}
