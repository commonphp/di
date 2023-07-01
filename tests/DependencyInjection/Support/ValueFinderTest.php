<?php

namespace CommonPHP\Tests\DependencyInjection\Support;

use CommonPHP\DependencyInjection\Support\ValueFinder;
use CommonPHP\Tests\Fixtures\ExampleClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionFunction;

class ValueFinderTest extends TestCase
{
    private ValueFinder $valueFinder;

    protected function setUp(): void
    {
        $this->valueFinder = new ValueFinder();
        $this->valueFinder->onLookup(function (string $name, string $typeName, bool &$found): mixed {
            if ($name == 'lookupString' && $typeName == 'string') {
                $found = true;
                return 'value found!';
            } else if ($name == 'lookupInt' && $typeName == 'int') {
                $found = true;
                return 42;
            }
            return null;
        });
    }

    public function testFindParametersForClass(): void
    {
        $className = ExampleClass::class;
        $reflectedClass = new ReflectionClass($className);
        $constructor = $reflectedClass->getConstructor();
        $params = $this->valueFinder->findParameters($constructor, ['param1' => 'value1', 'param2' => 'value2']);

        // Assuming ExampleClass constructor has two parameters 'param1' and 'param2'
        $this->assertEquals(['value1', 'value2'], $params);
    }

    public function testFindParametersForFunction(): void
    {
        $functionName = 'exampleFunction';
        $reflectedFunction = new ReflectionFunction($functionName);
        $params = $this->valueFinder->findParameters($reflectedFunction, ['param1' => 'value1', 'param2' => 'value2']);

        // Assuming exampleFunction has two parameters 'param1' and 'param2'
        $this->assertEquals(['value1', 'value2'], $params);
    }

    public function testFindValue(): void
    {
        // This are used to get the "string" reflection type
        $className = ExampleClass::class;
        $reflectedClass = new ReflectionClass($className);

        $found = false;
        $value = $this->valueFinder->findValue('param1', $reflectedClass->getProperty('prop1')->getType(), ['param1' => 'value1', 'param2' => 'value2'], $found);

        $this->assertEquals('value1', $value);
        $this->assertTrue($found);
    }

    public function testFindValueNotFound(): void
    {
        // This are used to get the "string" reflection type
        $className = ExampleClass::class;
        $reflectedClass = new ReflectionClass($className);

        $found = false;
        $value = $this->valueFinder->findValue('param3', $reflectedClass->getProperty('prop1')->getType(), ['param1' => 'value1', 'param2' => 'value2'], $found);

        $this->assertNull($value);
        $this->assertFalse($found);
    }

    public function testFindValueLookup(): void
    {
        // This are used to get the "string" reflection type
        $className = ExampleClass::class;
        $reflectedClass = new ReflectionClass($className);

        $found = false;
        $value = $this->valueFinder->findValue('lookupString', $reflectedClass->getProperty('prop1')->getType(), ['param1' => 'value1', 'param2' => 'value2'], $found);

        $this->assertEquals('value found!', $value);
        $this->assertTrue($found);
    }
}

