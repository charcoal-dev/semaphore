<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore\Exceptions;

use Charcoal\Base\Support\Helpers\ErrorHelper;

/**
 * Class SemaphoreUnlockException
 * @package Charcoal\Semaphore\Exceptions
 */
class SemaphoreUnlockException extends SemaphoreException
{
    public function __construct(
        string      $message = "",
        int         $code = 0,
        bool        $captureLastError = false,
        ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $captureLastError ?
            ErrorHelper::lastErrorToRuntimeException() : $previous);
    }
}