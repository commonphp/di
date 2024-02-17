<?php

/**
 * Represents an exception thrown when a class cannot be instantiated as required.
 *
 * This exception signals a failure in the instantiation process of a class, indicating an inability to create an
 * instance due to various potential issues.
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

class InstantiationFailedException extends DependencyInjectionException
{
    public function __construct(string $class, ?Throwable $previous = null)
    {
        parent::__construct("Failed to instantiate the class $class.", $previous);
        $this->code = 1505;
    }
}
