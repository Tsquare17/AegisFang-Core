<?php

namespace AegisFang\Tests;

use AegisFang\Container\Container;
use AegisFang\Router\Request;
use PHPUnit\Framework\TestCase;
use AegisFang\Router\Router;

class RouterTest extends TestCase
{
    protected $router;

    protected $container;

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
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

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
}
