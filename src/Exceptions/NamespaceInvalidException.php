<?php

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

/**
 * Exception thrown when a namespace is not a valid PHP namespace.
 */
class NamespaceInvalidException extends Exception
{
    /**
     * NamespaceInvalidException constructor.
     *
     * @param string         $namespace The namespace that is not a valid PHP namespace.
     * @param int            $code      The error code (default: 0).
     * @param Throwable|null $previous  The previous throwable used for chaining exceptions (default: null).
     */
    public function __construct(string $namespace, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The namespace $namespace is not a valid PHP namespace.", $code, $previous);
    }
}