<?php

namespace CommonPHP\DependencyInjection;

/**
 * The InstantiationStack class represents a stack of class names used during object instantiation.
 */
final class InstantiationStack
{
    /**
     * The array of class names.
     *
     * @var array
     */
    private array $elements = [];

    /**
     * Push a class name onto the stack.
     *
     * @param string $className The class name to push.
     * @return void
     */
    public function push(string $className): void
    {
        $this->elements[] = $className;
    }

    /**
     * Pop the topmost class name from the stack.
     *
     * @return void
     */
    public function pop(): void
    {
        array_pop($this->elements);
    }

    /**
     * Check if a class is on the stack
     *
     * @param string $className The name of the class to check for
     * @return bool
     */
    public function has(string $className): bool
    {
        return in_array($className, $this->elements);
    }

    /**
     * Get the string representation of the stack.
     *
     * @return string The string representation of the stack.
     */
    public function toString()
    {
        return implode(', ', $this->elements);
    }
}