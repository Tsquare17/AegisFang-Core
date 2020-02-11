<?php

namespace AegisFang\Tests;

use AegisFang\Container\Container;
use PHPUnit\Framework\TestCase;
use AegisFang\Router\Router;

class RouterPostRequestTest extends TestCase
{
    protected Router $router;

    protected Container $container;

    public function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->router = Router::load(__DIR__ . '/../Fixtures/routes.php');
        $this->container = new Container();
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
