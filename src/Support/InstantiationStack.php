<?php

/**
 * This file contains the InstantiationStack class, which is a part of the
 * CommonPHP\DependencyInjection\Support namespace. It is responsible for
 * tracking the instantiation of classes during the process of dependency
 * injection, helping to identify and prevent circular dependencies.
 *
 * The InstantiationStack class provides a stack-based mechanism for keeping
 * track of which classes are currently being instantiated. This is essential for
 * detecting circular dependencies, which could otherwise lead to infinite loops or
 * other serious issues.
 *
 * This class is declared as final to prevent it from being extended, thus
 * ensuring the integrity of the dependency injection system.
 *
 * PHP version 8.1
 *
 * @package    CommonPHP
 * @subpackage DependencyInjection\Support
 * @author     Timothy McClatchey <timothy@commonphp.org>
 * @copyright  2023 CommonPHP.org
 * @license    http://opensource.org/licenses/MIT MIT License
 */

namespace CommonPHP\DependencyInjection\Support;

final class InstantiationStack
{
    /** @var class-string[] */
    private array $elements = [];

    /**
     * Adds a class name to the stack. This should be called when the instantiation
     * process for a class begins.
     *
     * @param class-string $className The class name to push.
     */
    public function push(string $className): void
    {
        $this->elements[] = $className;
    }

    /**
     * Removes the most recent class name from the stack. This should be called
     * when the instantiation process for a class completes.
     */
    public function pop(): void
    {
        array_pop($this->elements);
    }

    /**
     * @param class-string $className
     * @return bool
     */
    public function has(string $className): bool
    {
        return in_array($className, $this->elements);
    }

    public function toString(): string
    {
        return implode(', ', $this->elements);
    }
}