<?php

/**
 * This class is a part of the CommonPHP\DependencyInjection namespace.
 * It provides a dependency injection container that is able to instantiate, invoke, call, and populate objects.
 *
 * The DependencyInjector class leverages PHP's Reflection API to analyse and manage dependencies. It supports
 * checks for circular references during instantiation and handles parameter values using the ValueFinder class.
 * It provides a flexible and robust dependency injection solution for your PHP projects.
 *
 * PHP version 8.1
 *
 * @package    CommonPHP
 * @subpackage DependencyInjection
 * @author     Timothy McClatchey <timothy@commonphp.org>
 * @copyright  2023 CommonPHP.org
 * @license    http://opensource.org/licenses/MIT MIT License
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
use CommonPHP\DependencyInjection\Exceptions\UnsupportedReflectionTypeException;
use CommonPHP\DependencyInjection\Support\ValueFinder;
use CommonPHP\DependencyInjection\Support\InstantiationStack;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use Throwable;

final class DependencyInjector
{
    public ValueFinder $valueFinder;
    private InstantiationStack $instantiationStack;

    public function __construct()
    {
        $this->instantiationStack = new InstantiationStack();
        $this->valueFinder = new ValueFinder();
    }

    /**
     * @param class-string $className The fully qualified class name to instantiate
     * @param array $parameters The parameters to pass to the constructor
     * @return object The instantiated object.
     * @throws ClassNotDefinedException
     * @throws ClassNotInstantiableException
     * @throws ParameterDiscoveryFailedException
     * @throws UnsupportedReflectionTypeException
     * @throws InstantiateCircularReferenceException
     * @throws InstantiationFailedException
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
     * @param object $object The object to invoke against
     * @param string $methodName The name of the method
     * @param array $parameters Defined parameters to send to the method
     * @param bool $publicOnly Only allow public methods to be called
     * @return mixed
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
     * @param string|Closure $callable The function or closure to call
     * @param array $parameters The parameters passed
     * @return mixed
     * @throws CallFailedException
     */
    public function call(string|Closure $callable, array $parameters = []): mixed
    {
        try {
            $function = new ReflectionFunction($callable);
            return $function->invokeArgs($this->valueFinder->findParameters($function, $parameters));
        } catch (Throwable $t) {
            throw new CallFailedException(is_string($callable) ? 'callable' : '{Closure}', previous: $t);
        }
    }

    /**
     * @param object $object The object to populate properties on
     * @param array $values Array of values that contain any extra properties
     * @param bool $publicOnly Flag to indicate if the only public properties are to be populated
     * @throws UnsupportedReflectionTypeException
     */
    public function populate(object $object, array $values, bool $publicOnly = true): void
    {
        // Create a reflection of the object.
        $class = new ReflectionClass($object);

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
    }
}