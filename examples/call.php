<?php

require_once 'vendor/autoload.php';

use CommonPHP\DependencyInjection\DependencyInjector;

function exampleFunction(string $message)
{
    echo "The message is: " . $message;
}

$injector = new DependencyInjector();

try {
    // Call the function 'exampleFunction' with 'Hello, world!' as the parameter
    $injector->call('exampleFunction', ['message' => 'Hello, world!']);
} catch (Exception $e) {
    // Handle exceptions
    echo "An error occurred: " . $e->getMessage();
}
