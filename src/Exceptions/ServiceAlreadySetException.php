<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when a service is already set.
 */
class ServiceAlreadySetException extends Exception
{
    /**
     * ServiceAlreadySetException constructor.
     *
     * @param string         $class     The class of the service that is already registered.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The service $class cannot be manually set because it's already been set.", $code, $previous);
    }
}