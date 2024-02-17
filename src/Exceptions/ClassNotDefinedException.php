<?php

/**
 * Represents an exception thrown when a specified class is not defined.
 *
 * This exception indicates that the class intended for instantiation or usage within the dependency injection process
 * does not exist, highlighting issues with class resolution.
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

class ClassNotDefinedException extends DependencyInjectionException
{
    public function __construct(string $class, ?Throwable $previous = null)
    {
        parent::__construct("The class $class is not defined.", $previous);
        $this->code = 1502;
    }
}
