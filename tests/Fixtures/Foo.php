<?php

namespace AegisFang\Tests\Fixtures;

class Foo
{
    public $foo;

    public function __construct(Bar $bar)
    {
        $this->foo = $bar->bar;
    }

    public function baz(): string
    {
        return 'baz';
    }
}
