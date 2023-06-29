<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when a property is not defined in a class.
 */
class PropertyNotDefinedException extends Exception
{
    /**
     * PropertyNotDefinedException constructor.
     *
     * @param string         $class     The class in which the property is not defined.
     * @param string         $property  The property that is not defined.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, string $property, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The property $property is not defined in class $class.", $code, $previous);
    }
}