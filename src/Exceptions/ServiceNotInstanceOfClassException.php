<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when a service is not an instance or a subclass of the specified service class.
 */
class ServiceNotInstanceOfClassException extends Exception
{
    /**
     * ServiceNotInstanceOfClassException constructor.
     *
     * @param string         $class     The class of the service that it should be an instance or a subclass of.
     * @param string         $service   The class of the service
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, string $service, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The service object $service is not an instance or a subclass of the service class $class.", $code, $previous);
    }
}