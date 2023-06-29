<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when an alias has not been registered with a class.
 */
class AliasNotRegisteredException extends Exception
{
    /**
     * AliasNotRegisteredException constructor.
     *
     * @param string         $alias     The alias that has not been registered.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $alias, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The alias $alias has not been registered with a class.", $code, $previous);
    }
}