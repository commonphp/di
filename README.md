# CommonPHP Dependency Injection

`CommonPHP\DependencyInjection` is a simple yet powerful dependency injection (DI) container for PHP applications, enabling the means to dynamically create new objects, invoke methods, call functions and populate properties without having to know the structure of the target directly.

## Features

- Constructor and method injection: Instantiate classes and invoke methods with automatic resolution of dependencies.
- Value finder: A support class for the DI process that assists in discovering and managing parameter values during instantiation.
- Handling of circular dependencies: An instantiation stack to prevent issues caused by recursive dependency chains.
- Customizable parameter lookup: Enhance the default lookup process by adding custom lookup hooks.
- Populating object properties: Assign values to an object's properties, handling a variety of visibility and type scenarios.
- Exception handling: A series of specific exceptions to help troubleshoot issues related to DI and instantiation.

## Requirements

The CommonPHP framework requires PHP 8.3 or newer. This library specifically has the following dependencies:

- PHP's built-in Reflection classes, which are used extensively for instantiating classes, invoking methods, and reading type information.


## Installation

You can install `CommonPHP\DependencyInjection` using [Composer](https://getcomposer.org/):

```bash
composer require comphp/di
```

## Basic Usage

### Instantiating Classes

The main component is the `DependencyInjector` class, which is responsible for injecting the passed parameters into a constructor, method or function. 

```php
<?php

require_once 'vendor/autoload.php';

use CommonPHP\DependencyInjection\DependencyInjector;

$injector = new DependencyInjector();

// Instantiate a class
$exampleClass = $injector->instantiate(ExampleClass::class);

// Invoke a method
$result = $injector->invoke($exampleClass, 'exampleMethod');

// Call a function or closure
$result = $injector->call('exampleFunction');
```

### Populating Object Properties

The `DependencyInjector` can also populate the properties of an object:

```php
<?php

require_once 'vendor/autoload.php';

use CommonPHP\DependencyInjection\DependencyInjector;

$injector = new DependencyInjector();
$object = new stdClass();
$values = ['property1' => 'value1', 'property2' => 'value2'];

// populate public properties only
$injector->populate($object, $values);

// populate all properties
$injector->populate($object, $values, false);
```

### Custom Lookup Hooks

The `ValueFinder` class allows you to add custom lookup hooks:

```php
<?php

require_once 'vendor/autoload.php';

use CommonPHP\DependencyInjection\DependencyInjector;

$injector = new DependencyInjector();

$injector->valueFinder->onLookup(function (string $name, string $typeName, bool &$found): mixed {
    if ($typeName == MyClassType::class) {
        $found = true;
        return new MyClassType();
    } else if ($name == 'specificStringVariable') {
        $found = true;
        return 'specificStringValue';
    }
    return null;
});
```

## Documentation

For more in-depth documentation, check out [the Wiki](https://github.com/commonphp/di/wiki).

### API Reference

This is a high-level overview of the API. For detailed information about classes, methods, and properties, please refer to the source code and accompanying PHPDoc comments.

- **`CommonPHP\DependencyInjection\DependencyInjector`**: The main class in the library. Provides methods for instantiating classes, invoking methods, calling functions, and populating properties with automatic dependency resolution.

    - **`instantiate(string $class, array $params = []): object`**: Instantiates a class with the provided parameters.

    - **`invoke($object, string $method, array $params = [], bool $publicOnly = true): mixed`**: Invokes a method on a given object with the provided parameters.

    - **`call(callable $callable, array $params = []): mixed`**: Calls a function or closure with the provided parameters.

    - **`populate(object $object, array $values, bool $publicOnly = true): void`**: Populates the properties of an object with the given values.

- **`CommonPHP\DependencyInjection\Support\ValueFinder`**: A supporting class that assists in discovering and managing parameter values during instantiation.

    - **`onLookup(callable $callback): void`**: Registers a callback function to be used during the lookup process.

### Examples

Here are some examples of using CommonPHP\DependencyInjection. You can find the full source code for these examples in the `examples` directory of this repository.

- [**Instantiate**](https://github.com/commonphp/di/blob/master/examples/instantiate.php): This example shows how to instantiate a class using `DependencyInjector::instantiate()`.

- [**Invoke**](https://github.com/commonphp/di/blob/master/examples/invoke.php): This example shows how to invoke a method using `DependencyInjector::invoke()`.

- [**Call**](https://github.com/commonphp/di/blob/master/examples/call.php): This example shows how to call a function or closure using `DependencyInjector::call()`.

- [**Populate**](https://github.com/commonphp/di/blob/master/examples/populate.php): This example shows how to populate the properties of an object using `DependencyInjector::populate()`.

- [**Lookup Hooks**](https://github.com/commonphp/di/blob/master/examples/lookup-hooks.php): This example shows how to use a custom lookup hook with `ValueFinder::onLookup()`.


## Contributing

Contributions are always welcome! Please read the [contribution guidelines](CONTRIBUTING.md) first.

## Testing

This project uses PHPUnit for unit testing. Follow the instructions below to run the tests:

1. Ensure you have PHPUnit installed. If not, you can install it with Composer:

    ```bash
    composer require --dev phpunit/phpunit
    ```

2. Navigate to the project's root directory.

3. Run the tests using the following command:

    ```bash
    ./vendor/bin/phpunit tests
    ```

4. If the tests are successful, you will see output similar to:

    ```
    PHPUnit 9.6.9 by Sebastian Bergmann and contributors.

    ......................                                            22 / 22 (100%)

    Time: 00:00.228, Memory: 4.00 MB

    OK (22 tests, 36 assertions)
    ```

We recommend regularly running these tests during development to help catch any potential issues early. We also strive for a high level of test coverage, and additions to the codebase should ideally include corresponding tests.

For more detailed output or for integration into continuous integration (CI) systems, PHPUnit can generate a log file in a variety of formats. Check the [PHPUnit documentation](https://phpunit.de/documentation.html) for more information.

## License

This project is licensed under the MIT License. See the [LICENSE.md](LICENSE.md) file for details.
