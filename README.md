# CommonPHP Dependency Injection Library

This library provides a straightforward way to manage service dependencies in a PHP application. It follows the Inversion of Control principle, allowing classes to specify their dependencies without being responsible for their creation.

## Features

1. **Dependency Injection**: Enables classes to declare their dependencies in the constructor, which will be automatically injected when the class is instantiated.

2. **Service Container**: The library includes a service container that manages service instances. Services are created as singletons - once a service is created, the same instance will be returned every time it is requested.

3. **Simple API**: The library provides simple and intuitive methods for registering services and retrieving them from the service container.

## Usage

Here is a simple example of how to use the library:

```php
class MyClass {
    public function __construct(CommonPHP\DependencyInjection\ServiceContainer $container)
    {
        // The service container is injected as a dependency
    }
}

$di = new CommonPHP\DependencyInjection\DependencyInjector();
$container = new CommonPHP\DependencyInjection\ServiceContainer($di);

// Register your services
$di->services->register(MyClass::class);

// Retrieve the service
$myClassInstance = $container->get(MyClass::class);
```

This library is designed to prevent direct dependency on the `DependencyInjector` inside services. The `DependencyInjector` should only be used during application bootstrapping.

## Installation

This section outlines how to install the `CommonPHP Dependency Injection` library.

### Requirements

- PHP 8.1 or newer.

### With Composer

The easiest way to install the `CommonPHP Dependency Injection` library is via Composer.

If you don't have Composer installed, you can download it from [https://getcomposer.org/](https://getcomposer.org/).

Once you have Composer installed, you can install the `CommonPHP Dependency Injection` library by running the following command in your terminal:

```bash
composer require comphp/di
```

This command will add the `CommonPHP Dependency Injection` library as a dependency to your project, and Composer will automatically handle the autoloading of classes.

### Manual Installation

If you don't use Composer, you can download the latest release of the `CommonPHP Dependency Injection` library from the GitHub repository. After downloading, you will have to handle autoloading the classes manually in your application.

After the installation, you can use the library as outlined in the Usage section.

## Next Steps

After the installation, refer to the Usage section of this README to learn how to use the `CommonPHP Dependency Injection` library in your projects.

## Testing

As of version 0.0.1, we are still in the process of refining the codebase and defining the overall functionality of the CommonPHP Dependency Injection library, as well as other libraries within the CommonPHP framework. Therefore, comprehensive unit tests have not been created yet.

However, quality and reliability are paramount to us. Rest assured, we plan to incorporate robust testing in future versions as the functionality becomes more concrete. In the meantime, we encourage contributions in all areas of the project, including tests.

If you are interested in contributing to testing, or any other aspect of this project, please see the 'Contributing' section for more information.


## License

This project is licensed under the MIT License. See the [LICENSE.md](LICENSE.md) file for details.
