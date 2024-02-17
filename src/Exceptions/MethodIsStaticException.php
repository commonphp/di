<?php

/**
 * Represents an exception thrown when an attempt is made to non-statically invoke a static method.
 *
 * This exception indicates a misuse of a static method within the dependency injection process, particularly when an
 * attempt is made to invoke it in a non-static context.
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

class MethodIsStaticException extends DependencyInjectionException
{
    public function __construct(string $class, string $method, ?Throwable $previous = null)
    {
        parent::__construct("The method $method in class $class is static.", $previous);
        $this->code = 1507;
    }
}
