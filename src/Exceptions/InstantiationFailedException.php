<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when the instantiation of a class fails.
 */
class InstantiationFailedException extends Exception
{
    /**
     * InstantiationFailedException constructor.
     *
     * @param string         $class     The class that failed to be instantiated.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Failed to instantiate the class $class.", $code, $previous);
    }
}