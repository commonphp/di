<?php

/**
 * Represents an exception thrown when an unsupported reflection type is encountered.
 *
 * This exception is thrown to indicate that the dependency injection process has encountered a reflection type that it
 * does not support, potentially due to a limitation or an unrecognized type.
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

class UnsupportedReflectionTypeException extends DependencyInjectionException
{
    public function __construct(string $name, ?Throwable $previous = null)
    {
        parent::__construct("Unsupported ReflectionType encountered in Dependency Injector: $name.", $previous);
        $this->code = 1511;
    }
}
