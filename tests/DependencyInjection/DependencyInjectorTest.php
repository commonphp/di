<?php

namespace CommonPHP\Tests\DependencyInjection;

use CommonPHP\DependencyInjection\DependencyInjector;
use CommonPHP\DependencyInjection\Exceptions\CallFailedException;
use CommonPHP\DependencyInjection\Exceptions\ClassNotDefinedException;
use CommonPHP\DependencyInjection\Exceptions\ClassNotInstantiableException;
use CommonPHP\DependencyInjection\Exceptions\InstantiateCircularReferenceException;
use CommonPHP\DependencyInjection\Exceptions\InstantiationFailedException;
use CommonPHP\DependencyInjection\Exceptions\InvocationFailedException;
use CommonPHP\DependencyInjection\Exceptions\MethodIsStaticException;
use CommonPHP\DependencyInjection\Exceptions\MethodNotDefinedException;
use CommonPHP\DependencyInjection\Exceptions\MethodNotPublicException;
use CommonPHP\DependencyInjection\Exceptions\UnsupportedReflectionTypeException;
use CommonPHP\Tests\Fixtures\AbstractClass;
use CommonPHP\Tests\Fixtures\CircularReferenceClass;
use CommonPHP\Tests\Fixtures\ClassContract;
use CommonPHP\Tests\Fixtures\ClassWithExceptionConstructor;
use CommonPHP\Tests\Fixtures\ExampleClass;
use CommonPHP\Tests\Fixtures\InvokeTargetClass;
use CommonPHP\Tests\Fixtures\PopulateClass;
use CommonPHP\Tests\Fixtures\SingletonClass;
use PHPUnit\Framework\TestCase;

class DependencyInjectorTest extends TestCase
{
    private DependencyInjector $di;
    protected function setUp(): void
    {
        $singletonClass = null;
        $this->di = new DependencyInjector();
        $this->di->valueFinder->onLookup(function (string $name, string $typeName, bool &$found) use ($singletonClass): mixed {
            if ($typeName == SingletonClass::class) {
                if ($singletonClass === null) $singletonClass = $this->di->instantiate(SingletonClass::class, []);
                $found = true;
                return $singletonClass;
            } else if ($typeName == CircularReferenceClass::class) {
                return $this->di->instantiate($typeName, []);
            }
            return null;
        });
    }

    public function testInstantiateSuccess(): void
    {
        $this->assertIsObject($this->di->instantiate(ExampleClass::class, [
            'param1' => 'Hello, World!',
            'param2' => 42
        ]));
    }

    public function testInstantiateFailClassNotDefined(): void
    {
        $this->expectException(ClassNotDefinedException::class);
        $this->di->instantiate(ClassContract::class, []);
        $this->di->instantiate('InvalidClassName', []);
    }

    public function testInstantiateFailCircularReference(): void
    {
        $this->expectException(InstantiateCircularReferenceException::class);
        $this->di->instantiate(CircularReferenceClass::class, []);
    }

    public function testInstantiateFailNotInstantiable(): void
    {
        $this->expectException(ClassNotInstantiableException::class);
        $this->di->instantiate(AbstractClass::class, []);
    }


    public function testInvokeSuccess(): void
    {
        $obj = $this->di->instantiate(InvokeTargetClass::class, []);
        $this->assertEquals('public', $this->di->invoke($obj, 'publicMethod'));
    }

    public function testInvokeSuccessNonPublic(): void
    {
        $obj = $this->di->instantiate(InvokeTargetClass::class, []);
        $this->assertEquals('private', $this->di->invoke($obj, 'privateMethod', publicOnly: false));
        $this->assertEquals('protected', $this->di->invoke($obj, 'protectedMethod', publicOnly: false));
    }

    public function testInvokeFailMethodNotDefined(): void
    {
        $this->expectException(MethodNotDefinedException::class);
        $obj = $this->di->instantiate(InvokeTargetClass::class, []);
        $this->di->invoke($obj, 'missingMethod');
    }

    public function testInvokeFailMethodIsStatic(): void
    {
        $this->expectException(MethodIsStaticException::class);
        $obj = $this->di->instantiate(InvokeTargetClass::class, []);
        $this->di->invoke($obj, 'staticMethod');
    }

    public function testInvokeFailMethodNotPublic(): void
    {
        $this->expectException(MethodNotPublicException::class);
        $obj = $this->di->instantiate(InvokeTargetClass::class, []);
        $this->di->invoke($obj, 'privateMethod');
        $this->di->invoke($obj, 'protectedMethod');
    }

    public function testInvokeFailReflection(): void
    {
        $this->expectException(InvocationFailedException::class);
        $obj = $this->di->instantiate(InvokeTargetClass::class, []);
        $this->di->invoke($obj, 'methodWithException');
    }

    public function testCallSuccess(): void
    {
        $this->assertEquals('function', $this->di->call('exampleFunction', [
            'param1' => 'Hello, World!',
            'param2' => 42
        ]));
        $this->assertEquals('closure', $this->di->call(function () {
            return 'closure';
        }));
    }

    public function testCallFailedReflection(): void
    {
        $this->expectException(CallFailedException::class);
        $this->di->call(function (string|int $param) {
            // ...
        });
    }

    public function testPopulateSuccess(): void
    {
        $values = [
            'prop1' => 'Hello, World!',
            'prop2' => 42,
            'prop3' => true,
            'readonlyProp1' => 1,
            'readonlyProp2' => 2
        ];
        $obj = $this->di->instantiate(PopulateClass::class, []);
        $class = new \ReflectionClass($obj);
        $this->di->populate($obj, $values);

        $this->assertEquals($values['prop1'], $obj->prop1);
        $this->assertEquals($values['prop2'], $obj->prop2);
        $this->assertNotEquals($values['prop3'], $class->getProperty('prop3')->getValue($obj));
        $this->assertEquals($values['readonlyProp1'], $obj->readonlyProp1);
        $this->assertNotEquals($values['readonlyProp2'], $obj->readonlyProp2);
    }
}