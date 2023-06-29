<?php

include '../vendor/autoload.php';

class InstantiateExample1
{
    public function __construct(CommonPHP\DependencyInjection\ServiceContainer $container)
    {
        // This is the expected usage, and will work. The DI will not expose itself
    }
}

class InstantiateExample2
{
    public function __construct(CommonPHP\DependencyInjection\DependencyInjector $di)
    {
        // This instantiate will fail because DI will never expose itself. DI is only used during application bootstrapping
        // then the ServiceContainer auto-registers itself as service and provides a simplified readonly method of handling
        // services
    }
}

$di = new CommonPHP\DependencyInjection\DependencyInjector();
$container = new CommonPHP\DependencyInjection\ServiceContainer($di);

// This is a simulated "bootstrapping"
$di->services->register(InstantiateExample1::class);
$di->services->register(InstantiateExample2::class);

$ex1 = $container->get(InstantiateExample1::class); // This will work
$ex2 = $container->get(InstantiateExample2::class); // This will not