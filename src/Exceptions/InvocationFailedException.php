<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when the invocation of a method fails.
 */
class InvocationFailedException extends Exception
{
    /**
     * InvocationFailedException constructor.
     *
     * @param string         $class     The class in which the method invocation failed.
     * @param string         $method    The method that failed to be invoked.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, string $method, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Invocation of method $method in class $class failed.", $code, $previous);
    }
}