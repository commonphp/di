<?php

require_once 'vendor/autoload.php';

use CommonPHP\DependencyInjection\DependencyInjector;

class ExampleClass
{
    public string $message;
}

$injector = new DependencyInjector();

// Create a new instance of ExampleClass
$instance = new ExampleClass();

try {
    // Populate the properties of the instance
    $injector->populate($instance, ['message' => 'Hello, world!']);
} catch (Exception $e) {
    // Handle exceptions
    echo "An error occurred: " . $e->getMessage();
}

echo $instance->message;  // Outputs: "Hello, world!"
