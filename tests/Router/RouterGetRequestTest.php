<?php

namespace AegisFang\Tests;

use AegisFang\Container\Container;
use PHPUnit\Framework\TestCase;
use AegisFang\Router\Router;

class RouterGetRequestTest extends TestCase
{
    protected Router $router;

    protected Container $container;

    public function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->router = Router::load(__DIR__ . '/../Fixtures/routes.php');
        $this->container = new Container();
    }

    /** @test */
    public function routes_are_loaded(): void
    {
        $this->assertInstanceOf(Router::class, $this->router);
    }

    /** @test */
    public function route_defining_methods_define_their_respective_routes(): void
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];

        foreach ($methods as $method) {
            $this->router->{strtolower($method)}([
                '/' => $method . '::route',
            ]);
        }

        $routes = $this->router->getRoutes();

        foreach ($methods as $method) {
            $this->assertEquals($method . '::route', $routes['/'][$method]);
        }
    }

    /** @test */
    public function basic_route_returns_content(): void
    {
        $this->router->get([
            '/' => static function () {
                return 'test';
            }
        ]);

        $this->assertEquals('test', $this->router->direct($this->container, '/')->getContent());
    }

    /** @test */
    public function controller_route_returns_content(): void
    {
        $this->router->get([
            '/' => '\AegisFang\Tests\Fixtures\Foo::baz'
        ]);

        $this->assertEquals('baz', $this->router->direct($this->container, '/')->getContent());
    }

    /** @test */
    public function rest_route_returns_content(): void
    {
        $this->router->rest([
            '/wildcard' => static function () {
                return 'test';
            }
        ]);

        $this->assertEquals('test', $this->router->direct($this->container, '/wildcard')->getContent());
    }
}
