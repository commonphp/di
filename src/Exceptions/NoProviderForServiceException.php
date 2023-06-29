<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when a service provider cannot be found for the specified class
 */
class NoProviderForServiceException extends Exception
{
    /**
     * ServiceNotFoundException constructor.
     *
     * @param string         $class     The class of the requested service that was not found.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("There were no service providers available that supports the class $class.", $code, $previous);
    }
}