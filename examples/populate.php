<?php

require_once '../vendor/autoload.php';

use CommonPHP\DependencyInjection\DependencyInjector;

class ExampleClass
{
    public string $message;
}

$injector = new DependencyInjector();

// Create a new instance of ExampleClass
$instance = new ExampleClass();

// Populate the properties of the instance
try {
    $injector->populate($instance, ['message' => 'Hello, world!']);
} catch (\CommonPHP\DependencyInjection\Exceptions\UnsupportedReflectionTypeException $e) {
    // This exception is thrown when there's an unsupported reflection type encountered during property discovery.
    // Proper error message should be returned or logged, and the error should be handled appropriately.
    die($e);
}

echo $instance->message;  // Outputs: "Hello, world!"
