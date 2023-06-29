<?php

namespace CommonPHP\DependencyInjection;

use Closure;
use CommonPHP\DependencyInjection\Collections\AliasCollection;
use CommonPHP\DependencyInjection\Collections\NamespaceCollection;
use CommonPHP\DependencyInjection\Collections\ProviderCollection;
use CommonPHP\DependencyInjection\Collections\ServiceCollection;
use CommonPHP\DependencyInjection\Exceptions\CallFailedException;
use CommonPHP\DependencyInjection\Exceptions\ClassNotDefinedException;
use CommonPHP\DependencyInjection\Exceptions\ClassNotInstantiableException;
use CommonPHP\DependencyInjection\Exceptions\InstantiateCircularReferenceException;
use CommonPHP\DependencyInjection\Exceptions\InstantiationFailedException;
use CommonPHP\DependencyInjection\Exceptions\InvocationFailedException;
use CommonPHP\DependencyInjection\Exceptions\MethodIsStaticException;
use CommonPHP\DependencyInjection\Exceptions\MethodNotDefinedException;
use CommonPHP\DependencyInjection\Exceptions\MethodNotPublicException;
use CommonPHP\DependencyInjection\Exceptions\ParameterDiscoveryFailedException;
use CommonPHP\DependencyInjection\Exceptions\PropertyIsStaticException;
use CommonPHP\DependencyInjection\Exceptions\PropertyNotDefinedException;
use CommonPHP\DependencyInjection\Exceptions\PropertyNotPublicException;
use CommonPHP\DependencyInjection\Exceptions\UnsupportedReflectionTypeException;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Throwable;

/**
 * The DependencyInjector class is responsible for dependency injection and service management.
 */
final class DependencyInjector
{
    /**
     * The collection of aliases.
     *
     * @var AliasCollection
     */
    public readonly AliasCollection $aliases;

    /**
     * The collection of namespaces.
     *
     * @var NamespaceCollection
     */
    public readonly NamespaceCollection $namespaces;

    /**
     * The collection of service providers.
     *
     * @var ProviderCollection
     */
    public readonly ProviderCollection $providers;

    /**
     * The collection of services.
     *
     * @var ServiceCollection
     */
    public readonly ServiceCollection $services;

    /**
     * The current instantiation stack
     *
     * @var InstantiationStack
     */
    private InstantiationStack $stack;

    /**
     * DependencyInjector constructor.
     */
    public function __construct()
    {
        $this->aliases = new AliasCollection();
        $this->namespaces = new NamespaceCollection();
        $this->providers = new ProviderCollection($this);
        $this->services = new ServiceCollection($this);
        $this->stack = new InstantiationStack();
    }

    /**
     * Find a service object by its class name.
     *
     * @param string $className The class name of the service to find.
     * @return null|object Returns an object if the service is found, otherwise false
     */
    public function findService(string $className): ?object
    {

        // If a service alias exists for the class name, get the actual class name
        $realClass = $this->aliases->has($className) ? $this->aliases->get($className) : $className;

        // If the service can be handled by a provider, let the provider do it
        if ($this->providers->has($realClass)) {
            return $this->providers->get($realClass);
        }

        // If a service isn't registered under the class name, try to register it
        if ($this->namespaces->has($realClass)) {
            $this->services->register($realClass);
        }

        return $this->services->has($className) ? $this->services->get($className) : null;
    }

    /**
     * Get the parameter value for a given ReflectionType.
     *
     * @param ReflectionType $types  The ReflectionType instance.
     * @param bool           $found Flag to indicate if the parameter value was found.
     * @return mixed The parameter value.
     */
    public function getParameter(ReflectionType $types, bool &$found): mixed
    {
        $found = false;

        // If the type is a named type or a union type, it is converted to an array.
        if ($types instanceof ReflectionNamedType) {
            $types = [$types];
        } else if ($types instanceof ReflectionUnionType) {
            $types = $types->getTypes();
        } else {
            // An exception is thrown if the type is not a named type or a union type.
            throw new UnsupportedReflectionTypeException(get_class($types));
        }

        // Iterate over the types. If a matching service is found, it is returned.
        foreach ($types as $type)
        {
            if ($type->isBuiltin()) continue;
            $service = $this->findService($type->getName());
            if ($service !== null)
            {
                $found = true;
                return $service;
            }
        }

        // If no matching service is found, null is returned.
        return null;
    }

    /**
     * Build the parameters for a given ReflectionFunction or ReflectionMethod.
     *
     * @param ReflectionFunction|ReflectionMethod $source     The source ReflectionFunction or ReflectionMethod.
     * @param array                               $parameters The parameters to use for instantiation.
     * @return array The built parameters.
     */
    public function buildParameters(ReflectionFunction|ReflectionMethod $source, array $parameters): array
    {
        // If the method or function does not need any parameters, return an empty array.
        if ($source->getNumberOfParameters() == 0) return [];
        $result = [];

        // Iterate over each parameter needed by the function or method.
        foreach ($source->getParameters() as $parameter)
        {
            $found = false;
            // Try to get the parameter.
            $result[] = $this->getParameter($parameter->getType(), $found);

            // If the parameter was not found, check if it exists in the provided parameters array.
            if (!$found) {
                if (array_key_exists($parameter->getName(), $parameters)) {
                    $result[] = $parameters[$parameter->getName()];
                    continue;
                }

                // If the parameter has a default value, use that.
                if ($parameter->isDefaultValueAvailable()) {
                    $result[] = $parameter->getDefaultValue();
                    continue;
                }

                // If the parameter allows null values, use null.
                if ($parameter->allowsNull())
                {
                    $result[] = null;
                    continue;
                }

                // If the parameter is still not found, throw an exception.
                throw new ParameterDiscoveryFailedException(
                    $source instanceof ReflectionMethod ? $source->getDeclaringClass()->getName() : '{main}',
                    $source->getName(),
                    $parameter->getName()
                );
            }
        }

        // Return the built parameters array.
        return $result;
    }

    /**
     * Instantiate a class with the given parameters.
     *
     * @param string $className  The class name to instantiate.
     * @param array  $parameters The parameters to use for instantiation.
     * @return object The instantiated object.
     */
    public function instantiate(string $className, array $parameters): object
    {
        // Check if the class exists, if not throw an exception.
        if (!class_exists($className)) {
            throw new ClassNotDefinedException($className);
        }

        // If the class is a service class, return the existing instance or merge the parameters with the stored parameters.
        if ($this->services->has($className) && $this->services->isAvailable($className)) {
            return $this->services->get($className, $parameters);
        }

        // Check for circular reference, if found throw an exception.
        if ($this->stack->has($className)) {
            throw new InstantiateCircularReferenceException($className, $this->stack->toString());
        }

        // Add the class name to the instantiation stack.
        $this->stack->push($className);

        // Create a reflection of the class.
        $class = new ReflectionClass($className);

        // Check if the class is instantiable, if not throw an exception.
        if (!$class->isInstantiable()) {
            throw new ClassNotInstantiableException($className);
        }

        // Get the class constructor.
        $constructor = $class->getConstructor();
        try {
            if ($constructor === null)
            {
                // If no constructor, create a new instance of the class without any parameters.
                $result = $class->newInstance();
            }
            else
            {
                // If a constructor exists, create a new instance of the class with the built parameters.
                $result = $class->newInstanceArgs($this->buildParameters($constructor, $parameters));
            }
        } catch (ReflectionException $e) {
            // If instantiation fails, throw an exception.
            throw new InstantiationFailedException($class->getName(), previous: $e);
        }

        // Remove the class name from the instantiation stack.
        $this->stack->pop();

        return $result;
    }

    /**
     * Invoke a method on an object with the given parameters.
     *
     * @param object $object      The object on which to invoke the method.
     * @param string $methodName  The name of the method to invoke.
     * @param array  $parameters  The parameters to use for method invocation.
     * @param bool   $publicOnly  Flag to indicate if only public methods should be invoked.
     * @return mixed The result of the method invocation.
     */
    public function invoke(object $object, string $methodName, array $parameters = [], bool $publicOnly = true): mixed
    {
        // Create a reflection of the object.
        $class = new ReflectionClass($object);

        // If the method does not exist, throw an exception.
        if (!$class->hasMethod($methodName)) {
            throw new MethodNotDefinedException($class->getName(), $methodName);
        }

        // Get the method.
        $method = $class->getMethod($methodName);

        // If the method is static, throw an exception.
        if ($method->isStatic()) {
            throw new MethodIsStaticException($class->getName(), $methodName);
        }

        // If only public methods are allowed and the method is not public, throw an exception.
        if ($publicOnly && !$method->isPublic()) {
            throw new MethodNotPublicException($class->getName(), $methodName);
        }

        try {
            // Invoke the method with the built parameters.
            return $method->invokeArgs($object, $this->buildParameters($method, $parameters));
        } catch (Throwable $t) {
            throw new InvocationFailedException($class->getName(), $methodName, previous: $t);
        }
    }

    /**
     * Call a callable with the given parameters.
     *
     * @param string|Closure $callable   The callable to call.
     * @param array          $parameters The parameters to use for the callable.
     * @return mixed The result of the callable.
     */
    public function call(string|Closure $callable, array $parameters = []): mixed
    {
        try {
            // Create a reflection of the function.
            $function = new ReflectionFunction($callable);
            // Invoke the function with the built parameters.
            return $function->invokeArgs($this->buildParameters($function, $parameters));
        } catch (Throwable $t) {
            // If the function call fails, throw an exception.
            throw new CallFailedException(is_string($callable) ? 'callable' : '{Closure}', previous: $t);
        }
    }

    /**
     * Populate an object with the given values.
     *
     * @param object $object      The object to populate.
     * @param array  $values      The values to populate the object with.
     * @param bool   $publicOnly  Flag to indicate if only public properties should be populated.
     * @return object The populated object.
     */
    public function populate(object $object, array $values, bool $publicOnly = true): object
    {
        // Create a reflection of the object.
        $class = new ReflectionClass($object);

        // Iterate over each property to set.
        foreach ($values as $propertyName => $propertyValue) {
            // If the property does not exist, throw an exception.
            if (!$class->hasProperty($propertyName)) {
                throw new PropertyNotDefinedException($class->getName(), $propertyName);
            }

            // Get the property.
            $property = $class->getProperty($propertyName);

            // If the property is static, throw an exception.
            if ($property->isStatic()) {
                throw new PropertyIsStaticException($class->getName(), $propertyName);
            }

            // If only public properties are allowed and the property is not public, throw an exception.
            if ($publicOnly && !$property->isPublic()) {
                throw new PropertyNotPublicException($class->getName(), $propertyName);
            }

            // Set the property value.
            $property->setValue($object, $propertyValue);
        }

        return $object;
    }
}