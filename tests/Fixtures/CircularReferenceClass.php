<?php

namespace CommonPHP\Tests\Fixtures;

class CircularReferenceClass
{
    public function __construct(CircularReferenceClass $circularReferenceClass)
    {
    }
}