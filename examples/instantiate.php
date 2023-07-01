<?php

require_once 'vendor/autoload.php';

use CommonPHP\DependencyInjection\DependencyInjector;

class ExampleClass
{
    public function __construct(private string $property)
    {
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}

$injector = new DependencyInjector();

try {
    // Instantiate ExampleClass with a provided constructor parameter
    $instance = $injector->instantiate(ExampleClass::class, ['property' => 'exampleValue']);

    // Use the instantiated object
    echo $instance->getProperty();  // Outputs: exampleValue
} catch (Exception $e) {
    // Handle exceptions
    echo "An error occurred: " . $e->getMessage();
}
