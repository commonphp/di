<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when a call to a function fails.
 */
class CallFailedException extends Exception
{
    /**
     * CallFailedException constructor.
     *
     * @param string         $function  The function that failed to be called.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $function, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Call to function $function failed.", $code, $previous);
    }
}