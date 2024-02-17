<?php

/**
 * Defines a base exception for dependency injection errors.
 *
 * This exception is thrown when a general error occurs within the dependency injection process, serving as a base for more specific dependency injection exceptions.
 *
 * @package CommonPHP
 * @subpackage DependencyInjection\Exceptions
 * @author Timothy McClatchey <timothy@commonphp.org>
 * @copyright 2024 CommonPHP.org
 * @license http://opensource.org/licenses/MIT MIT License
 * @noinspection PhpUnused
 */

namespace CommonPHP\DependencyInjection\Exceptions;

use Exception;
use Throwable;

class DependencyInjectionException extends Exception
{
    public function __construct(string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, 1500, $previous);
    }
}