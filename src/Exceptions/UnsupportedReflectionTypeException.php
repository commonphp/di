<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when an unsupported ReflectionType is encountered in the Dependency Injector.
 */
class UnsupportedReflectionTypeException extends Exception
{
    /**
     * UnsupportedReflectionTypeException constructor.
     *
     * @param string         $name      The name of the unsupported ReflectionType.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $name, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Unsupported ReflectionType encountered in Dependency Injector: $name.", $code, $previous);
    }
}