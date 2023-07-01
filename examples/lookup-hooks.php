<?php

require_once 'vendor/autoload.php';

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

try {
    // Create a new instance of ExampleClass using the custom lookup hook
    $instance = $injector->instantiate(ExampleClass::class, []);
} catch (Exception $e) {
    // Handle exceptions
    echo "An error occurred: " . $e->getMessage();
}

echo $instance->message;  // Outputs: "Hello from custom lookup hook!"
