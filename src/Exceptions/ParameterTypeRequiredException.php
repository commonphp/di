<?php

/**
 * Thrown when a method parameter lacks a type declaration, essential for DI resolution.
 *
 * This exception is raised to highlight missing type declarations for parameters in method signatures,
 * which are required for the dependency injection system to accurately resolve and inject dependencies.
 * Ensures that method parameters within classes conform to type declaration standards necessary for
 * effective dependency management.
 *
 * @package CommonPHP
 * @subpackage DependencyInjection\Exceptions
 * @author Timothy McClatchey <timothy@commonphp.org>
 * @copyright 2024 CommonPHP.org
 * @license http://opensource.org/licenses/MIT MIT License
 * @noinspection PhpUnused
 */

namespace CommonPHP\DependencyInjection\Exceptions;

use Throwable;

class ParameterTypeRequiredException extends DependencyInjectionException
{
    public function __construct(string $class, string $method, string $parameter, ?Throwable $previous = null)
    {
        parent::__construct("Parameters must have a type on parameter $parameter for method $method in class $class.", $previous);
        $this->code = 1512;
    }
}
