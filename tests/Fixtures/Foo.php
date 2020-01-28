<?php

namespace AegisFang\Tests\Fixtures;

class Foo
{
    public Bar $bar;

    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }

    public function baz(): string
    {
        return 'baz';
    }
}
