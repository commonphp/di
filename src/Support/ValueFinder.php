<?php

/**
 * Supports the dependency injection process by managing the discovery and handling of parameter values
 * during object instantiation, leveraging PHP's Reflection API for introspection.
 *
 * @package CommonPHP
 * @subpackage DependencyInjection
 * @author Timothy <timothy@commonphp.org>
 * @copyright 2024 CommonPHP.org
 * @license http://opensource.org/licenses/MIT MIT License
 * @noinspection PhpUnused
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
    /**
     * @var Closure[] Array of lookup callbacks used for custom value resolution.
     */
    private array $onLookupCallbacks = [];

    /**
     * Registers a callback to be invoked during parameter value lookup, allowing for custom
     * resolution logic.
     *
     * @param callable|string $callback A callback or a string representing a function name.
     * @return void
     */
    public function onLookup(callable|string $callback): void
    {
        if (is_string($callback)) $callback = $callback(...);
        $this->onLookupCallbacks[] = $callback;
    }

    /**
     * Attempts to find a value for a specified parameter, considering passed parameters and
     * registered lookup callbacks.
     *
     * @param string $name The name of the parameter for which a value is being sought.
     * @param ReflectionType $types The expected types of the parameter as a ReflectionType instance.
     * @param array $passedParameters An associative array of parameters passed to the injector.
     * @param bool &$found Reference flag indicating whether the value was found.
     * @return mixed The resolved value for the parameter, or null if not found and null is allowed.
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
     * Resolves the parameters needed for a given ReflectionFunction or ReflectionMethod, using both
     * explicitly passed parameters and those resolved through registered lookup callbacks.
     *
     * @param ReflectionFunction|ReflectionMethod $source The reflection of the function or method being called.
     * @param array $passedParameters An associative array of explicitly passed parameters.
     * @return array An array of resolved parameters suitable for invoking the function or method.
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
            $newValue = $this->findValue($parameter->getName(), $parameter->getType(), $passedParameters, $found);

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
            else
            {
                $result[] = $newValue;
            }
        }

        // Return the built parameters array.
        return $result;
    }
}