<?php

/**
 * Represents an exception thrown due to a circular reference detected during instantiation.
 *
 * This exception indicates a circular dependency within the instantiation process, which prevents successful
 * dependency injection due to the recursion loop.
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

class InstantiateCircularReferenceException extends DependencyInjectionException
{
    public function __construct(string $class, string $stack, ?Throwable $previous = null)
    {
        parent::__construct("Circular reference detected during instantiation of the class $class. Instantiation stack: $stack.", $previous);
        $this->code = 1504;
    }
}
