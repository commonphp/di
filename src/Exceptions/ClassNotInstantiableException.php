<?php

/**
 * Represents an exception thrown when a class cannot be instantiated.
 *
 * This exception is used to signal that a class, which is required to be instantiated as part of the dependency
 * injection process, is not instantiable, often due to it being abstract or an interface.
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

class ClassNotInstantiableException extends DependencyInjectionException
{
    public function __construct(string $class, ?Throwable $previous = null)
    {
        parent::__construct("The class $class is not instantiable.", $previous);
        $this->code = 1503;
    }
}
