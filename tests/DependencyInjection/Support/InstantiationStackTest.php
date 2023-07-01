<?php

namespace CommonPHP\Tests\DependencyInjection\Support;

use CommonPHP\DependencyInjection\Support\InstantiationStack;
use PHPUnit\Framework\TestCase;

class InstantiationStackTest extends TestCase
{
    private InstantiationStack $stack;

    protected function setUp(): void
    {
        $this->stack = new InstantiationStack();
    }

    public function testPushAndPop(): void
    {
        $this->assertFalse($this->stack->has('TestClass'));
        $this->stack->push('TestClass');
        $this->assertTrue($this->stack->has('TestClass'));
        $this->stack->pop();
        $this->assertFalse($this->stack->has('TestClass'));
    }

    public function testToString(): void
    {
        $this->stack->push('TestClass1');
        $this->stack->push('TestClass2');
        $this->assertEquals('TestClass1, TestClass2', $this->stack->toString());
    }

    public function testEmptyStack(): void
    {
        $this->assertFalse($this->stack->has('TestClass'));
        $this->assertEquals('', $this->stack->toString());
    }

    public function testDuplicatePush(): void
    {
        $this->stack->push('TestClass');
        $this->assertTrue($this->stack->has('TestClass'));
        $this->stack->push('TestClass');
        $this->assertTrue($this->stack->has('TestClass'));
        $this->assertEquals('TestClass, TestClass', $this->stack->toString());
    }
}
