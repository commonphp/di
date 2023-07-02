<?php

require_once 'vendor/autoload.php';

use CommonPHP\DependencyInjection\DependencyInjector;

function exampleFunction(string $message)
{
    echo "The message is: " . $message;
}

$injector = new DependencyInjector();

// Call the function 'exampleFunction' with 'Hello, world!' as the parameter
try {
    $injector->call('exampleFunction', ['message' => 'Hello, world!']);
} catch (CommonPHP\DependencyInjection\Exceptions\CallFailedException $e) {
    // This exception is thrown when an invocation of a method or function fails.
    // Proper error message should be returned or logged, and the error should be handled appropriately.
    die($e);
}