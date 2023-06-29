<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when attempting to access a non-public property.
 */
class PropertyNotPublicException extends Exception
{
    /**
     * PropertyNotPublicException constructor.
     *
     * @param string         $class     The class in which the property is declared.
     * @param string         $property  The non-public property that cannot be accessed.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, string $property, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The property $property in class $class is not public.", $code, $previous);
    }
}