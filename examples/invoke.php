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

try {
    // Instantiate ExampleClass without any constructor parameters
    $instance = $injector->instantiate(ExampleClass::class, []);

    // Invoke setProperty method on the instantiated object
    $injector->invoke($instance, 'setProperty', ['newValue' => 'Updated value']);

    // Use the updated object
    echo $instance->getProperty();  // Outputs: Updated value
} catch (Exception $e) {
    // Handle exceptions
    echo "An error occurred: " . $e->getMessage();
}
