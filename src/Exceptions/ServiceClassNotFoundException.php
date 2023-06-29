<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when the service class is not found.
 */
class ServiceClassNotFoundException extends Exception
{
    /**
     * ServiceClassNotFoundException constructor.
     *
     * @param string         $class     The class of the service that was not found.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The service class $class was not found.", $code, $previous);
    }
}