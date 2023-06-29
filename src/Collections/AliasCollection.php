<?php

namespace CommonPHP\DependencyInjection\Collections;

use CommonPHP\DependencyInjection\Exceptions\AliasAlreadyRegisteredException;
use CommonPHP\DependencyInjection\Exceptions\AliasClassNotDerivedException;
use CommonPHP\DependencyInjection\Exceptions\AliasClassNotFoundException;
use CommonPHP\DependencyInjection\Exceptions\AliasNotRegisteredException;
use CommonPHP\DependencyInjection\Exceptions\ServiceClassNotFoundException;

/**
 * The AliasCollection class represents a collection of aliases.
 */
final class AliasCollection
{
    /**
     * The array of aliases.
     *
     * @var array
     */
    private array $aliases = [];

    /**
     * Check if an alias exists in the collection.
     *
     * @param string $aliasClass The class name of the alias.
     * @return bool True if the alias exists, false otherwise.
     */
    public function has(string $aliasClass): bool
    {
        return array_key_exists($aliasClass, $this->aliases);
    }

    /**
     * Get the class name associated with an alias.
     *
     * @param string $aliasClass The class name of the alias.
     * @return string The class name associated with the alias.
     * @throws AliasNotRegisteredException If the alias is not found in the collection.
     */
    public function get(string $aliasClass): string
    {
        if (!$this->has($aliasClass)) {
            throw new AliasNotRegisteredException($aliasClass);
        }
        return $this->aliases[$aliasClass];
    }

    /**
     * Register an alias with a class name.
     *
     * @param string $aliasClass  The class name of the alias.
     * @param string $serviceClass The class name associated with the alias.
     * @return void
     * @throws AliasAlreadyRegisteredException If the alias is already registered with a different class.
     * @throws ServiceClassNotFoundException If the service class is not found.
     * @throws AliasClassNotFoundException If the alias class is not found.
     * @throws AliasClassNotDerivedException If the alias class and service class are not derived from each other.
     */
    public function register(string $aliasClass, string $serviceClass): void
    {
        if ($this->has($aliasClass)) {
            throw new AliasAlreadyRegisteredException($aliasClass, $serviceClass, $this->aliases[$aliasClass]);
        }

        if (!class_exists($serviceClass)) {
            throw new ServiceClassNotFoundException($serviceClass);
        }

        if (!class_exists($aliasClass)) {
            throw new AliasClassNotFoundException($aliasClass);
        }

        if (!is_subclass_of($aliasClass, $serviceClass) && !is_subclass_of($serviceClass, $aliasClass)) {
            throw new AliasClassNotDerivedException($aliasClass, $serviceClass);
        }

        $this->aliases[$aliasClass] = $serviceClass;
    }

    /**
     * Get all aliases as an array.
     *
     * @return array The aliases as an array.
     */
    public function toArray(): array
    {
        return $this->aliases;
    }
}