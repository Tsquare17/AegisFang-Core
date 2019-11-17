<?php

namespace AegisFang\Tests\Fixtures;

class Bar
{
    public function get(Foo $foo): Foo
    {
        return $foo;
    }
}
