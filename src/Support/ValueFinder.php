<?php

/**
 * This class is a part of the CommonPHP\DependencyInjection\Support namespace.
 * It acts as a support class for the dependency injection process, specifically
 * for finding and handling parameter values during instantiation.
 *
 * The ValueFinder class provides functionality to find and manage parameter values
 * required during the instantiation of classes. It leverages PHP's Reflection API
 * to discover parameters, supports callbacks for extensibility during lookup,
 * and handles a variety of edge cases to ensure robustness.
 *
 * PHP version 8.1
 *
 * @package    CommonPHP
 * @subpackage DependencyInjection\Support
 * @author     Timothy <timothy@commonphp.org>
 * @copyright  2023 CommonPHP.org
 * @license    http://opensource.org/licenses/MIT MIT License
 */

namespace CommonPHP\DependencyInjection\Support;

use Closure;
use CommonPHP\DependencyInjection\Exceptions\ParameterDiscoveryFailedException;
use CommonPHP\DependencyInjection\Exceptions\UnsupportedReflectionTypeException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

final class ValueFinder
{
    /** @var Closure[] */
    private array $onLookupCallbacks = [];

    /**
     * This hooks into the lookup callbacks when findValue(...) is called. Three parameters are sent
     * to the callback:
     *      - $name: The name of the value being looked for
     *      - $typeName: The expected type or class-name of the value being looked for
     *      - &$found: If the lookup finds the value, it must set this to true and return the value
     * The return type is expected to be: mixed
     *
     * @param callable|string $callback A list of callbacks
     * @return void
     */
    public function onLookup(callable|string $callback): void
    {
        if (is_string($callback)) $callback = $callback(...);
        $this->onLookupCallbacks[] = $callback;
    }

    /**
     * @param string $name The parameter name
     * @param ReflectionType $types The ReflectionType instance.
     * @param array $passedParameters Parameters that were passed for the injection
     * @param bool $found If the value was found or not
     * @return mixed
     * @throws UnsupportedReflectionTypeException
     */
    public function findValue(string $name, ReflectionType $types, array $passedParameters, bool &$found): mixed
    {
        // Check if a value was passed and, if so, return it
        if (array_key_exists($name, $passedParameters)) {
            $found = true;
            return $passedParameters[$name];
        }

        $found = false;
        $allowsNull = false;

        // If the type is a named type or a union type, it is converted to an array.
        if ($types instanceof ReflectionNamedType) {
            $types = [$types];
        } else if ($types instanceof ReflectionUnionType) {
            $types = $types->getTypes();
        } else {
            // An exception is thrown if the type is not a named type or a union type.
            throw new UnsupportedReflectionTypeException(get_class($types));
        }

        $value = null;

        // Iterate over the types. If a matching value is found, it is returned.
        foreach ($types as $type)
        {
            // Allow for value lookups to be extensible, hooking via the onLookup() method
            foreach ($this->onLookupCallbacks as $callback)
            {
                $value = $callback($name, $type->getName(), $found);
                if ($found) {
                    break 2;
                }
            }

            // Check if this type supports null and, if so, set the allowsNull flag for later use, but keep iterating
            // just in case the next type is found in the lookup callbacks
            if ($type->allowsNull() && !$allowsNull) {
                $allowsNull = true;
            }
        }

        // Null is supported
        if (!$found && $allowsNull) {
            $found = true;
            $value = null;
        }

        // If no matching service is found, null is returned.
        return $value;
    }

    /**
     * @param ReflectionFunction|ReflectionMethod $source The source ReflectionFunction or ReflectionMethod.
     * @param array $passedParameters The parameters passed to the DI
     * @return array
     * @throws ParameterDiscoveryFailedException
     * @throws UnsupportedReflectionTypeException
     */
    public function findParameters(ReflectionFunction|ReflectionMethod $source, array $passedParameters): array
    {
        // If the method or function does not need any parameters, return an empty array.
        if ($source->getNumberOfParameters() == 0) return [];
        $result = [];

        // Iterate over each parameter needed by the function or method.
        foreach ($source->getParameters() as $parameter)
        {
            $found = false;

            // Try to find the value
            $result[] = $this->findValue($parameter->getName(), $parameter->getType(), $passedParameters, $found);

            if (!$found) {

                // If the parameter has a default value, use that.
                if ($parameter->isDefaultValueAvailable()) {
                    $result[] = $parameter->getDefaultValue();
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
}