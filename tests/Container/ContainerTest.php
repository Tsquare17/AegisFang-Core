<?php

namespace AegisFang\Tests;

use AegisFang\Container\Container;
use AegisFang\Router\Router;
use AegisFang\Tests\Fixtures\Foo;
use AegisFang\Tests\Fixtures\Bar;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    protected Container $container;

    public function setUp(): void
    {
        $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../Fixtures/config';
        $_SERVER['REQUEST_METHOD'] = 'GET';
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
    public function can_inject_into_constructor(): void
    {
        $foo = $this->container->get('Foo.Key');

        $this->assertInstanceOf(Bar::class, $foo->bar);
    }

    /** @test */
    public function can_inject_into_method(): void
    {
        $bar = $this->container->get('Bar.Key');

        $barMethod = $this->container->getMethod($bar, 'get');

        $this->assertInstanceOf(Foo::class, $barMethod);
    }

    /** @test */
    public function can_inject_into_closure(): void
    {
        $router = Router::load(__DIR__ . '/../Fixtures/routes.php');
        $router->get([
            '/' => static function (Foo $foo) {
                return $foo->baz();
            }
        ]);

        $this->assertEquals('baz', $router->direct($this->container, '/')->getContent());
    }
}
