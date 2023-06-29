<?php

namespace CommonPHP\DependencyInjection\Contracts;

/**
 * The ServiceProviderContract interface defines the contract that service providers should implement.
 */
interface ServiceProviderContract
{
    /**
     * Check if the service provider supports the given class name.
     *
     * @param string $className The class name to check.
     * @return bool True if the service provider supports the class, false otherwise.
     */
    public function supports(string $className): bool;

    /**
     * Instantiate a service of the given class name with optional parameters.
     *
     * @param string $className  The class name of the service to instantiate.
     * @param array  $parameters Optional parameters to pass to the constructor.
     * @return object The instantiated service object.
     */
    public function instantiate(string $className, array $parameters = []): object;
}