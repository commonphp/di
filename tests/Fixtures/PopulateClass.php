<?php

namespace CommonPHP\Tests\Fixtures;

class PopulateClass
{
    public readonly int $readonlyProp1;
    public readonly int $readonlyProp2;
    public string $prop1;
    public int $prop2;
    public string $unusedProp;

    private bool $prop3 = false;

    public function __construct()
    {
        $this->readonlyProp2 = 0;
    }

    public function getProp3(): bool
    {
        return $this->prop3;
    }
}

include_once 'functions.php';