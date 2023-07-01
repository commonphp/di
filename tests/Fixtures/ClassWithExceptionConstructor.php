<?php

namespace CommonPHP\Tests\Fixtures;

class ClassWithExceptionConstructor
{
    public function __construct()
    {
        throw new \Exception();
    }
}

include_once 'functions.php';