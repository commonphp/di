<?php

/**
 * This file provides the core functionality for dependency injection within the CommonPHP framework,
 * leveraging PHP's Reflection API to dynamically manage object instantiation and method invocation.
 *
 * @package CommonPHP
 * @subpackage DependencyInjection
 * @author Timothy McClatchey <timothy@commonphp.org>
 * @copyright 2024 CommonPHP.org
 * @license http://opensource.org/licenses/MIT MIT License
 * @noinspection PhpUnused
 */

namespace CommonPHP\DependencyInjection;

use Closure;
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
use CommonPHP\DependencyInjection\Exceptions\ParameterTypeRequiredException;
use CommonPHP\DependencyInjection\Exceptions\UnsupportedReflectionTypeException;
use CommonPHP\DependencyInjection\Support\ValueFinder;
use CommonPHP\DependencyInjection\Support\InstantiationStack;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use Throwable;

final class DependencyInjector
{
    /** @var ValueFinder Provides mechanisms for discovering parameter values during object instantiation. */
    public readonly ValueFinder $valueFinder;

    /** @var InstantiationStack Tracks the instantiation process to prevent circular references. */
    private InstantiationStack $instantiationStack;

    /**
     * Initializes the DependencyInjector with necessary support structures.
     */
    public function __construct()
    {
        $this->instantiationStack = new InstantiationStack();
        $this->valueFinder = new ValueFinder();
    }

    /**
     * Assigns a custom callback for instantiating a specified class.
     *
     * Enables defining a custom instantiation logic for a specific class through a callback,
     * thereby overriding the default instantiation mechanism. This feature is particularly useful
     * for setting up complex objects or integrating classes requiring specific construction patterns,
     * enhancing flexibility in object creation within the dependency injection system.
     *
     * @param string   $className The fully qualified name of the class for custom instantiation.
     * @param callable $callback  A user-defined function that instantiates the class. The callback
     *                            is provided with the DependencyInjector instance, class name,
     *                            and type name, facilitating context-aware instantiation.
     * @return void
     */
    public function delegate(string $className, callable $callback): void
    {
        $this->valueFinder->onLookup(function ($name, $typeName, &$found) use ($className, $callback) {
            if ($typeName === $className) {
                $found = true;
                return $callback($this, $name, $typeName);
            }
            return null;
        });
    }

    /**
     * Instantiates a class with the given parameters, resolving dependencies automatically.
     *
     * @template T
     * @param class-string<T> $className The fully qualified class name to instantiate.
     * @param array $parameters Parameters to pass to the constructor, if any.
     * @return T The instantiated object.
     * @throws ClassNotDefinedException
     * @throws ClassNotInstantiableException
     * @throws ParameterDiscoveryFailedException
     * @throws UnsupportedReflectionTypeException
     * @throws InstantiateCircularReferenceException
     * @throws InstantiationFailedException
     * @throws ParameterTypeRequiredException
     */
    public function instantiate(string $className, array $parameters): object
    {
        // Check if the class exists, if not throw an exception.
        if (!class_exists($className)) {
            throw new ClassNotDefinedException($className);
        }

        // Check for circular reference, if found throw an exception.
        if ($this->instantiationStack->has($className)) {
            throw new InstantiateCircularReferenceException($className, $this->instantiationStack->toString());
        }

        // Add the class name to the instantiation stack.
        $this->instantiationStack->push($className);

        // Create a reflection of the class.
        $class = new ReflectionClass($className);

        // Check if the class is instantiable, if not throw an exception.
        if (!$class->isInstantiable()) {
            throw new ClassNotInstantiableException($className);
        }

        // Get the class constructor.
        $constructor = $class->getConstructor();

        try {
            $result = $constructor === null ?
                $class->newInstance() :
                $class->newInstanceArgs($this->valueFinder->findParameters($constructor, $parameters));
        } catch (ReflectionException $e) {
            throw new InstantiationFailedException($class->getName(), previous: $e);
        }

        // Remove the class name from the instantiation stack.
        $this->instantiationStack->pop();

        return $result;
    }

    /**
     * Invokes a method on the given object with specified parameters.
     *
     * @param object $object The object on which to invoke the method.
     * @param string $methodName The method name to invoke.
     * @param array $parameters Parameters to pass to the method.
     * @param bool $publicOnly Whether to restrict invocation to public methods only.
     * @return mixed The result of the method invocation.
     * @throws InvocationFailedException
     * @throws MethodIsStaticException
     * @throws MethodNotDefinedException
     * @throws MethodNotPublicException
     */
    public function invoke(object $object, string $methodName, array $parameters = [], bool $publicOnly = true): mixed
    {
        $class = new ReflectionClass($object);

        if (!$class->hasMethod($methodName)) {
            throw new MethodNotDefinedException($class->getName(), $methodName);
        }

        $method = $class->getMethod($methodName);

        // Static method are not supported
        if ($method->isStatic()) {
            throw new MethodIsStaticException($class->getName(), $methodName);
        }

        // If only public methods are allowed and the method is not public, throw an exception.
        if ($publicOnly && !$method->isPublic()) {
            throw new MethodNotPublicException($class->getName(), $methodName);
        }

        try {
            return $method->invokeArgs($object, $this->valueFinder->findParameters($method, $parameters));
        } catch (Throwable $t) {
            throw new InvocationFailedException($class->getName(), $methodName, previous: $t);
        }
    }

    /**
     * Calls a function or closure with specified parameters.
     *
     * @param string|Closure $callable The function or closure to call.
     * @param array $parameters Parameters to pass to the callable.
     * @return mixed The result of the callable invocation.
     * @throws CallFailedException
     */
    public function call(string|Closure $callable, array $parameters = []): mixed
    {
        try {
            $function = new ReflectionFunction($callable);
            return $function->invokeArgs($this->valueFinder->findParameters($function, $parameters));
        } catch (Throwable $t) {
            throw new CallFailedException(is_string($callable) ? "callable '$callable'" : '{Closure}', previous: $t);
        }
    }

    /**
     * Populates the properties of an object with the given values.
     *
     * @param object $object The object to populate.
     * @param array $values Key-value pairs for property assignments.
     * @param bool $publicOnly Whether to restrict population to public properties only.
     * @throws UnsupportedReflectionTypeException
     */
    public function populate(object $object, array $values, bool $publicOnly = true): void
    {
        // Create a reflection of the object.
        $class = new ReflectionClass($object);
        while ($class !== false)
        {
            foreach ($class->getProperties() as $property) {
                // Static properties are not supported. Also ignore properties that are not public, if the publicOnly
                // value is set to true
                if ($property->isStatic() || ($publicOnly && !$property->isPublic())) {
                    continue;
                }

                // Ignore readonly properties that have already been set
                if ($property->isReadOnly() && $property->isInitialized($object)) {
                    continue;
                }

                $found = false;
                $value = $this->valueFinder->findValue($property->getName(), $property->getType(), $values, $found);
                if ($found) {
                    $property->setValue($object, $value);
                }
            }
            $class = $class->getParentClass();
        }
    }
}
