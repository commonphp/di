<?php

namespace CommonPHP\DependencyInjection;

use CommonPHP\DependencyInjection\Exceptions\ServiceNotFoundException;

/**
 * The ServiceContainer class represents a container for managing services.
 */
final class ServiceContainer
{
    /**
     * The DependencyInjector instance.
     *
     * @var DependencyInjector
     */
    private DependencyInjector $di;

    /**
     * ServiceContainer constructor.
     *
     * @param DependencyInjector $di The DependencyInjector instance.
     */
    public function __construct(DependencyInjector $di)
    {
        $this->di = $di;
        $di->services->set(ServiceContainer::class, $this);
    }

    /**
     * Check if a service with the given class name exists in the container.
     *
     * @param string $className The class name of the service.
     * @return bool True if the service exists, false otherwise.
     */
    public function has(string $className): bool
    {
        return $this->di->findService($className) !== null;
    }

    /**
     * Get the service object with the given class name from the container.
     *
     * @param string $className The class name of the service.
     * @return object The service object.
     * @throws ServiceNotFoundException If the service is not found in the container.
     */
    public function get(string $className): object
    {
        $service = $this->di->findService($className);

        // If service doesn't exist, throw an exception
        if ($service === null) {
            throw new ServiceNotFoundException($className);
        }

        return $service;
    }
}