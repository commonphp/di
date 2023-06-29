<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when an alias is already registered for a service in the Dependency Injection container.
 */
class AliasAlreadyRegisteredException extends Exception
{
    /**
     * AliasAlreadyRegisteredException constructor.
     *
     * @param string         $class     The class that the alias can't be used for.
     * @param string         $alias     The alias that is already registered.
     * @param string         $service   The service that the alias is registered for.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, string $alias, string $service, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The alias $alias is already registered for the service $service, it can't be used for the service $class.", $code, $previous);
    }
}