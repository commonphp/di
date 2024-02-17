<?php

/**
 * Represents an exception thrown when a function call fails during dependency injection.
 *
 * This exception is used specifically when a call to a function or method fails within the dependency injection process,
 * indicating problems with invoking the desired functionality.
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

class CallFailedException extends DependencyInjectionException
{
    public function __construct(string $function, ?Throwable $previous = null)
    {
        parent::__construct("Call to function $function failed.", $previous);
        $this->code = 1501;
    }
}
