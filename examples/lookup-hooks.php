<?php

require_once '../vendor/autoload.php';

use CommonPHP\DependencyInjection\DependencyInjector;
use CommonPHP\DependencyInjection\Support\ValueFinder;

class ExampleClass
{
    public function __construct(public string $message)
    {
    }
}

$injector = new DependencyInjector();

// Add a custom lookup hook to the ValueFinder
$injector->valueFinder->onLookup(function (string $name, string $typeName, bool &$found): mixed {
    if ($name === 'message' && $typeName === 'string') {
        $found = true;
        return 'Hello from custom lookup hook!';
    }

    return null;
});

// Create a new instance of ExampleClass using the custom lookup hook
try {
    $instance = $injector->instantiate(ExampleClass::class, []);
} catch (\CommonPHP\DependencyInjection\Exceptions\ClassNotDefinedException $e) {
    // This exception is thrown when a class is not defined but attempted to be instantiated.
    // Proper error message should be returned or logged, and the error should be handled appropriately.
    die($e);
} catch (\CommonPHP\DependencyInjection\Exceptions\ClassNotInstantiableException $e) {
    // This exception is thrown when a class that is not instantiable (like an interface or abstract class) is attempted to be instantiated.
    // Proper error message should be returned or logged, and the error should be handled appropriately.
    die($e);
} catch (\CommonPHP\DependencyInjection\Exceptions\InstantiateCircularReferenceException $e) {
    // This exception is thrown when there is a circular reference detected during instantiation of a class.
    // Proper error message should be returned or logged, and the error should be handled appropriately.
    die($e);
} catch (\CommonPHP\DependencyInjection\Exceptions\InstantiationFailedException $e) {
    // This exception is thrown when instantiation of a class fails for some reason.
    // Proper error message should be returned or logged, and the error should be handled appropriately.
    die($e);
} catch (\CommonPHP\DependencyInjection\Exceptions\ParameterDiscoveryFailedException $e) {
    // This exception is thrown when there's a failure in discovering the parameters required for instantiation of a class.
    // Proper error message should be returned or logged, and the error should be handled appropriately.
    die($e);
} catch (\CommonPHP\DependencyInjection\Exceptions\UnsupportedReflectionTypeException $e) {
    // This exception is thrown when there's an unsupported reflection type encountered during parameter discovery.
    // Proper error message should be returned or logged, and the error should be handled appropriately.
    die($e);
}

echo $instance->message;  // Outputs: "Hello from custom lookup hook!"
