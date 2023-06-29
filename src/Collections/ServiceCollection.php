<?php

namespace CommonPHP\DependencyInjection\Collections;

use CommonPHP\DependencyInjection\Exceptions\ServiceAlreadyRegisteredException;
use CommonPHP\DependencyInjection\Exceptions\ServiceAlreadySetException;
use CommonPHP\DependencyInjection\Exceptions\ServiceClassNotFoundException;
use CommonPHP\DependencyInjection\Exceptions\ServiceNotInstanceOfClassException;
use CommonPHP\DependencyInjection\Exceptions\ServiceNotFoundException;
use CommonPHP\DependencyInjection\DependencyInjector;

/**
 * The ServiceCollection class represents a collection of services.
 */
final class ServiceCollection
{
    /**
     * The array of services.
     *
     * @var array
     */
    private array $services = [];

    /**
     * The DependencyInjector instance.
     *
     * @var DependencyInjector
     */
    private DependencyInjector $di;

    /**
     * ServiceCollection constructor.
     *
     * @param DependencyInjector $di The DependencyInjector instance.
     */
    public function __construct(DependencyInjector $di)
    {
        $this->di = $di;
    }

    /**
     * Check if a service exists in the collection.
     *
     * @param string $className The class name of the service.
     * @return bool True if the service exists, false otherwise.
     */
    public function has(string $className): bool
    {
        return array_key_exists($className, $this->services);
    }

    /**
     * Get the service object from the collection.
     *
     * @param string $className The class name of the service.
     * @param array $extraParameters If the service will be instantiated at this point, include the extra parameters
     * @return object The service object.
     * @throws ServiceNotFoundException If the service is not found in the collection.
     */
    public function get(string $className, array $extraParameters = []): object
    {
        if (!$this->has($className)) {
            throw new ServiceNotFoundException($className);
        }
        if (is_array($this->services[$className])) {
            $parameters = array_merge($extraParameters, $this->services[$className]);
            $this->services[$className] = $this->di->instantiate($className, $parameters);
        }
        return $this->services[$className];
    }

    /**
     * Register a service with optional parameters.
     *
     * @param string $className The class name of the service.
     * @param array $parameters The parameters to use for service instantiation.
     * @return void
     * @throws ServiceClassNotFoundException If the service class is not found.
     * @throws ServiceAlreadyRegisteredException If the service is already registered.
     */
    public function register(string $className, array $parameters = []): void
    {
        if (!class_exists($className)) {
            throw new ServiceClassNotFoundException($className);
        }

        if (array_key_exists($className, $this->services)) {
            throw new ServiceAlreadyRegisteredException($className);
        }

        $this->services[$className] = $parameters;
    }

    /**
     * Set a service instance in the collection.
     *
     * @param string $className The class name of the service.
     * @param object $serviceInstance The service instance to set.
     * @param bool $autoRegister Whether to automatically register the service if it is not found.
     * @return void
     * @throws ServiceNotFoundException If the service is not found in the collection and autoRegister is false.
     * @throws ServiceAlreadyRegisteredException If the service is already set in the collection.
     * @throws ServiceNotInstanceOfClassException If the service instance is not an instance or a subclass of the service class.
     * @throws ServiceClassNotFoundException
     */
    public function set(string $className, object $serviceInstance, bool $autoRegister = true): void
    {
        if (!$this->has($className)) {
            if (!$autoRegister) {
                throw new ServiceNotFoundException($className);
            }
            $this->register($className);
        }

        if (!is_array($this->services[$className])) {
            throw new ServiceAlreadySetException($className);
        }

        if (!is_subclass_of($serviceInstance, $className) && get_class($serviceInstance) != $className) {
            throw new ServiceNotInstanceOfClassException($className, get_class($serviceInstance));
        }

        $this->services[$className] = $serviceInstance;
    }

    /**
     * Get all services as an array.
     *
     * @return array The services as an array.
     */
    public function toArray(): array
    {
        return $this->services;
    }

    /**
     * Check if a class has been instantiated
     *
     * @param string $className The class to check for
     * @return bool
     */
    public function isAvailable(string $className): bool
    {
        if (!array_key_exists($className, $this->services)) {
            throw new ServiceNotFoundException($className);
        }
        return is_object($this->services[$className]);
    }
}