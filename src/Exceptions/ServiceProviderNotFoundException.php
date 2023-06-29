<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when a service provider class is not found.
 */
class ServiceProviderNotFoundException extends Exception
{
    /**
     * ServiceProviderNotFoundException constructor.
     *
     * @param string         $class     The class of the service provider that was not found.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The service provider class $class was not found.", $code, $previous);
    }
}