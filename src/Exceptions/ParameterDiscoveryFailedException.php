<?php

/**
 * Represents an exception thrown when parameter discovery for a method fails.
 *
 * This exception indicates a failure in resolving the parameters required for invoking a method, crucial for the
 * dependency injection process to successfully wire dependencies.
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

class ParameterDiscoveryFailedException extends DependencyInjectionException
{
    public function __construct(string $class, string $method, string $parameter, ?Throwable $previous = null)
    {
        parent::__construct("Failed to discover parameter $parameter for method $method in class $class.", $previous);
        $this->code = 1510;
    }
}
