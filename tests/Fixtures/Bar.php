<?php

namespace AegisFang\Tests\Fixtures;

class Bar
{
    public $bar;

    public function __construct()
    {
        $this->bar = 'bar';
    }

    public function get(Foo $foo): string
    {
        return $foo->baz();
    }
}
