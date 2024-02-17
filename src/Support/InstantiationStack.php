<?php


/**
 * Manages a stack of class names to track the instantiation process during dependency injection.
 *
 * This class provides a mechanism to track class instantiations in a stack-based approach,
 * essential for detecting and preventing circular dependencies in the dependency injection process.
 * Declared as final to maintain the integrity of the dependency injection system by preventing extension.
 *
 * @package CommonPHP
 * @subpackage DependencyInjection
 * @author Timothy McClatchey <timothy@commonphp.org>
 * @copyright 2024 CommonPHP.org
 * @license http://opensource.org/licenses/MIT MIT License
 * @noinspection PhpUnused
 */

namespace CommonPHP\DependencyInjection\Support;

final class InstantiationStack
{
    /**
     * Stack of class names being instantiated.
     *
     * @var class-string[]
     */
    private array $elements = [];

    /**
     * Adds a class name to the instantiation stack.
     *
     * This method is invoked at the beginning of a class instantiation process,
     * aiding in the detection of circular dependencies.
     *
     * @param class-string $className The name of the class being instantiated.
     */
    public function push(string $className): void
    {
        $this->elements[] = $className;
    }

    /**
     * Removes the most recent class name from the instantiation stack.
     *
     * This method is invoked upon the completion of a class's instantiation process.
     */
    public function pop(): void
    {
        array_pop($this->elements);
    }

    /**
     * Checks if a class name exists in the instantiation stack.
     *
     * @param class-string $className The name of the class to check.
     * @return bool True if the class name is in the stack, false otherwise.
     */
    public function has(string $className): bool
    {
        return in_array($className, $this->elements);
    }

    /**
     * Converts the instantiation stack to a string representation.
     *
     * @return string A comma-separated list of class names in the stack.
     */
    public function toString(): string
    {
        return implode(', ', $this->elements);
    }
}