<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when the alias class and the service class are not derived from each other.
 */
class AliasClassNotDerivedException extends Exception
{
    /**
     * AliasClassNotDerivedException constructor.
     *
     * @param string         $class     The class that the alias and service are not derived from each other.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $class, string $alias, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The alias class $alias and the service class $class are not derived from each other.", $code, $previous);
    }
}