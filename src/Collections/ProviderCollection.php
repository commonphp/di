<?php

namespace CommonPHP\DependencyInjection\Collections;

use CommonPHP\DependencyInjection\Contracts\ServiceProviderContract;
use CommonPHP\DependencyInjection\Exceptions\NoProviderForServiceException;
use CommonPHP\DependencyInjection\Exceptions\ServiceProviderAlreadyRegisteredException;
use CommonPHP\DependencyInjection\Exceptions\ServiceProviderMissingContractException;
use CommonPHP\DependencyInjection\Exceptions\ServiceProviderNotFoundException;
use CommonPHP\DependencyInjection\DependencyInjector;

/**
 * The ProviderCollection class represents a collection of service providers.
 */
final class ProviderCollection
{
    /**
     * The array of service providers.
     *
     * @var ServiceProviderContract[]
     */
    private array $providers = [];

    /**
     * The DependencyInjector instance.
     *
     * @var DependencyInjector
     */
    private DependencyInjector $di;

    /**
     * ProviderCollection constructor.
     *
     * @param DependencyInjector $di The DependencyInjector instance.
     */
    public function __construct(DependencyInjector $di)
    {
        $this->di = $di;
    }

    /**
     * Check if a service provider exists for the given class name.
     *
     * @param string $className The class name of the service.
     * @return bool True if a service provider exists, false otherwise.
     */
    public function has(string $className): bool
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($className)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the service object for the given class name using a service provider.
     *
     * @param string $className  The class name of the service.
     * @param array  $parameters The parameters to use for service instantiation.
     * @return object The service object.
     * @throws NoProviderForServiceException If no service provider is found for the given class name.
     */
    public function get(string $className, array $parameters = []): object
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($className)) {
                return $provider->instantiate($className, $parameters);
            }
        }
        throw new NoProviderForServiceException($className);
    }

    /**
     * Register a service provider with optional parameters.
     *
     * @param string $providerClass The class name of the service provider.
     * @param array  $parameters    The parameters to use for service provider instantiation.
     * @return void
     * @throws ServiceProviderNotFoundException If the service provider class is not found.
     * @throws ServiceProviderAlreadyRegisteredException If the service provider is already registered.
     * @throws ServiceProviderMissingContractException If the service provider does not implement ServiceProviderContract.
     */
    public function register(string $providerClass, array $parameters = []): void
    {
        if (!class_exists($providerClass)) {
            throw new ServiceProviderNotFoundException($providerClass);
        }

        if (array_key_exists($providerClass, $this->providers)) {
            throw new ServiceProviderAlreadyRegisteredException($providerClass);
        }

        if (!is_subclass_of($providerClass, ServiceProviderContract::class)) {
            throw new ServiceProviderMissingContractException($providerClass);
        }

        $parameters = array_merge($parameters, ['di' => $this->di]);
        $this->providers[$providerClass] = $this->di->instantiate($providerClass, $parameters);
    }

    /**
     * Get all registered service providers as an array.
     *
     * @return array The registered service providers.
     */
    public function toArray(): array
    {
        return $this->providers;
    }
}