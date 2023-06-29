<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when the alias class for a service is not found.
 */
class AliasClassNotFoundException extends Exception
{
    /**
     * AliasClassNotFoundException constructor.
     *
     * @param string         $class     The class for which the alias class was not found.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The alias class for service $class was not found.", $code, $previous);
    }
}