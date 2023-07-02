<?php

require_once 'vendor/autoload.php';

use CommonPHP\DependencyInjection\DependencyInjector;

class ExampleClass
{
    private string $property;

    public function __construct()
    {
        $this->property = 'Initial value';
    }

    public function setProperty(string $newValue): void
    {
        $this->property = $newValue;
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}

$injector = new DependencyInjector();
// Instantiate ExampleClass without any constructor parameters
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

// Invoke setProperty method on the instantiated object
try {
    $injector->invoke($instance, 'setProperty', ['newValue' => 'Updated value']);
} catch (\CommonPHP\DependencyInjection\Exceptions\InvocationFailedException $e) {
    // This exception is thrown when an invocation of a method or function fails.
    // Proper error message should be returned or logged, and the error should be handled appropriately.
    die($e);
} catch (\CommonPHP\DependencyInjection\Exceptions\MethodIsStaticException $e) {
    // This exception is thrown when an attempt is made to invoke a static method as if it was non-static.
    // Proper error message should be returned or logged, and the error should be handled appropriately.
    die($e);
} catch (\CommonPHP\DependencyInjection\Exceptions\MethodNotDefinedException $e) {
    // This exception is thrown when an attempt is made to invoke a method that is not defined.
    // Proper error message should be returned or logged, and the error should be handled appropriately.
    die($e);
} catch (\CommonPHP\DependencyInjection\Exceptions\MethodNotPublicException $e) {
    // This exception is thrown when an attempt is made to invoke a method that is not public.
    // Proper error message should be returned or logged, and the error should be handled appropriately.
    die($e);
}

// Use the updated object
echo $instance->getProperty();  // Outputs: Updated value