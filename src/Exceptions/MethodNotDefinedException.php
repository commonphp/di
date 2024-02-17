<?php

/**
 * Represents an exception thrown when a specified method is not defined in a class.
 *
 * This exception is thrown to indicate the absence of a required method in a class, highlighting a potential
 * misconfiguration or typo in the method name within the dependency injection process.
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

class MethodNotDefinedException extends DependencyInjectionException
{
    public function __construct(string $class, string $method, ?Throwable $previous = null)
    {
        parent::__construct("The method $method is not defined in class $class.", $previous);
        $this->code = 1508;
    }
}
