<?php

namespace AegisFang\Tests;

use AegisFang\Container\Container;
use AegisFang\Tests\Fixtures\Foo;
use AegisFang\Tests\Fixtures\Bar;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    protected $container;

    public function setUp(): void
    {
        $this->container = new Container();
        $this->container->set('Foo.Key', Foo::class);
        $this->container->set('Bar.Key', Bar::class);
    }

    /** @test */
    public function set_stores_dependencies(): void
    {
        $this->assertTrue($this->container->has('Foo.Key'));
        $this->assertTrue($this->container->has('Bar.Key'));
    }

    /** @test */
    public function get_retrieves_a_stored_dependency_by_key(): void
    {
        $foo = $this->container->get('Foo.Key');

        $this->assertInstanceOf(Foo::class, $foo);
    }

    /** @test */
    public function can_inject_bar_into_foo_constructor(): void
    {
        $foo = $this->container->get('Foo.Key');

        $this->assertInstanceOf(Bar::class, $foo->bar);
    }

    /** @test */
    public function can_inject_foo_into_bar_method(): void
    {
        $bar = $this->container->get('Bar.Key');

        $barMethod = $this->container->getMethod($bar, 'get');

        $this->assertInstanceOf(Foo::class, $barMethod);
    }
}
