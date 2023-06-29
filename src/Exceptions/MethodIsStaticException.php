<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when attempting to invoke a static method.
 */
class MethodIsStaticException extends Exception
{
    /**
     * MethodIsStaticException constructor.
     *
     * @param string         $class     The class in which the method is declared.
     * @param string         $method    The static method that cannot be invoked.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, string $method, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The method $method in class $class is static.", $code, $previous);
    }
}