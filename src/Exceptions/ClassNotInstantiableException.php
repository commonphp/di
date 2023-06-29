<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when a class is not instantiable.
 */
class ClassNotInstantiableException extends Exception
{
    /**
     * ClassNotInstantiableException constructor.
     *
     * @param string         $class     The class that is not instantiable.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The class $class is not instantiable.", $code, $previous);
    }
}