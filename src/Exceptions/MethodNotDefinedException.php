<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when a method is not defined in a class.
 */
class MethodNotDefinedException extends Exception
{
    /**
     * MethodNotDefinedException constructor.
     *
     * @param string         $class     The class in which the method is not defined.
     * @param string         $method    The method that is not defined.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, string $method, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The method $method is not defined in class $class.", $code, $previous);
    }
}