<?php

namespace CommonPHP\Tests\Fixtures;

class InvokeTargetClass
{
    public static function staticMethod()
    {
        return 'static';
    }
    public function publicMethod()
    {
        return 'public';
    }
    private function privateMethod()
    {
        return 'private';
    }
    protected function protectedMethod()
    {
        return 'protected';
    }

    public function methodWithException()
    {
        throw new \Exception();
    }
}