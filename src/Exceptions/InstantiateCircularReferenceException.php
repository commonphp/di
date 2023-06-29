<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when a circular reference is detected during class instantiation.
 */
class InstantiateCircularReferenceException extends Exception
{
    /**
     * InstantiateCircularReferenceException constructor.
     *
     * @param string         $class     The class for which a circular reference was detected.
     * @param string          $stack     The instantiation stack that caused the circular reference.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, string $stack, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Circular reference detected during instantiation of the class $class. Instantiation stack: $stack.", $code, $previous);
    }
}