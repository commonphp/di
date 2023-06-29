<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when a service provider does not implement the ServiceProviderContract.
 */
class ServiceProviderMissingContractException extends Exception
{
    /**
     * ServiceProviderMissingContractException constructor.
     *
     * @param string         $class     The class of the service provider that is missing the contract implementation.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The service provider $class does not implement ServiceProviderContract.", $code, $previous);
    }
}