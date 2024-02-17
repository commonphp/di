<?php

/**
 * Represents an exception thrown when a non-public method is invoked.
 *
 * This exception is used to signal an attempt to invoke a method that is not accessible due to its visibility, ensuring
 * that only public methods are invoked in the dependency injection process.
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

class MethodNotPublicException extends DependencyInjectionException
{
    public function __construct(string $class, string $method, ?Throwable $previous = null)
    {
        parent::__construct("The method $method in class $class is not public.", $previous);
        $this->code = 1509;
    }
}
