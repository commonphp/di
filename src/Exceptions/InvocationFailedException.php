<?php

/**
 * Represents an exception thrown when a method invocation fails.
 *
 * This exception is thrown to indicate that an attempt to invoke a method on a class instance failed during the
 * dependency injection process, signaling invocation-related issues.
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

class InvocationFailedException extends DependencyInjectionException
{
    public function __construct(string $class, string $method, ?Throwable $previous = null)
    {
        parent::__construct("Invocation of method $method in class $class failed.", $previous);
        $this->code = 1506;
    }
}
