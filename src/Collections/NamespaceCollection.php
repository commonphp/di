<?php

namespace CommonPHP\DependencyInjection\Collections;

use CommonPHP\DependencyInjection\Exceptions\NamespaceAlreadyRegisteredException;
use CommonPHP\DependencyInjection\Exceptions\NamespaceInvalidException;

/**
 * The NamespaceCollection class represents a collection of namespaces.
 */
final class NamespaceCollection
{
    /**
     * The array of namespaces.
     *
     * @var array
     */
    private array $namespaces = [];

    /**
     * Check if a class belongs to any registered namespace.
     *
     * @param string $className The fully qualified class name.
     * @return bool True if the class belongs to any registered namespace, false otherwise.
     */
    public function has(string $className): bool
    {
        foreach ($this->namespaces as $namespace) {
            if (str_starts_with($className, $namespace)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Register a namespace.
     *
     * @param string $namespace The namespace to register.
     * @return void
     * @throws NamespaceInvalidException If the namespace is not a valid PHP namespace.
     * @throws NamespaceAlreadyRegisteredException If the namespace is already registered.
     */
    public function register(string $namespace): void
    {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_\\\\]*$/m', $namespace)) {
            throw new NamespaceInvalidException($namespace);
        }

        if (!str_ends_with($namespace, "\\")) {
            $namespace .= "\\";
        }

        // If the namespace is already registered, throw an exception
        if (in_array($namespace, $this->namespaces)) {
            throw new NamespaceAlreadyRegisteredException($namespace);
        }

        // Register the namespace.
        $this->namespaces[] = $namespace;
    }

    /**
     * Get all registered namespaces as an array.
     *
     * @return array The registered namespaces.
     */
    public function toArray(): array
    {
        return $this->namespaces;
    }
}