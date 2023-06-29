<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when the discovery of a method parameter fails.
 */
class ParameterDiscoveryFailedException extends Exception
{
    /**
     * ParameterDiscoveryFailedException constructor.
     *
     * @param string         $class     The class in which the method is declared.
     * @param string         $method    The method in which the parameter discovery failed.
     * @param string         $parameter The parameter that failed to be discovered.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, string $method, string $parameter, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Failed to discover parameter $parameter for method $method in class $class.", $code, $previous);
    }
}