<?php

require_once '../vendor/autoload.php';

use CommonPHP\DependencyInjection\DependencyInjector;

// Assuming SpecificClass requires custom instantiation logic
class SpecificClass
{
    private string $customArg1;
    private string $customArg2;
    public function __construct(string $customArg1, string $customArg2)
    {
        $this->customArg1 = $customArg1;
        $this->customArg2 = $customArg2;
        // Custom construction logic here
    }
}

class InitiatedClass
{
    private SpecificClass $specificClass;
    public function __construct(SpecificClass $specificClass)
    {
        $this->specificClass = $specificClass;
    }
}

$injector = new DependencyInjector();

// Delegate the instantiation of SpecificClass to a custom callback
$injector->delegate(SpecificClass::class, function($injector, $name, $typeName) {
    // Example of using custom arguments
    $customArg1 = 'value1';
    $customArg2 = 'value2';

    return new SpecificClass($customArg1, $customArg2);
});

// Instantiate SpecificClass using the custom delegate logic
$instance = $injector->instantiate(InitiatedClass::class, []);

// Demonstrates that the $instance is indeed an object of SpecificClass
var_dump($instance);
