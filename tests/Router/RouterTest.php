<?php

namespace AegisFang\Tests;

use AegisFang\Container\Container;
use Fixtures\Middleware;
use Fixtures\SecondMiddleware;
use PHPUnit\Framework\TestCase;
use AegisFang\Router\Router;
use AegisFang\Tests\Fixtures\Rest;

/**
 * Class RouterGetRequestTest
 * @package AegisFang\Tests
 */
class RouterTest extends TestCase
{
    protected Router $router;

    protected Container $container;

    public function setUp(): void
    {
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

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->assertEquals('test', $this->router->direct($this->container, '/')->getContent());
    }

    /** @test */
    public function controller_route_returns_content(): void
    {
        $this->router->get([
            '/' => '\AegisFang\Tests\Fixtures\Foo::baz'
        ]);

        $_SERVER['REQUEST_METHOD'] = 'GET';

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

    /**
     * @test
     * @runInSeparateProcess
     */
    public function can_inject_url_parameters_in_get_route(): void
    {
        $this->router->get([
            '/' => '\AegisFang\Tests\Fixtures\Json::getUrlParam'
        ]);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [
            'id' => '3',
            'name' => 'tom',
        ];

        $this->router->direct($this->container, '/');

        $this->expectOutputString(json_encode([
            '3' => 'tom',
        ], JSON_THROW_ON_ERROR, 512));
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function rest_get_route_can_inject_parameters_and_outputs_content(): void
    {
        $this->router->rest([
            '/' => Rest::class
        ]);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [
            'id' => '5',
            'name' => 'ted',
        ];

        $this->router->direct($this->container, '/');

        $this->expectOutputString(json_encode([
            '5' => 'ted'
        ], JSON_THROW_ON_ERROR, 512));
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function rest_post_route_can_inject_parameters_and_output_content(): void
    {
        $this->router->rest([
            '/' => Rest::class
        ]);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['data'] = 'datum';

        $this->router->direct($this->container, '/');

        $this->expectOutputString(json_encode([
            'datum'
        ], JSON_THROW_ON_ERROR, 512));
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function rest_put_route_can_inject_parameters_and_output_content(): void
    {
        $this->router->rest([
            '/' => Rest::class
        ]);
        $_POST['REQUEST_METHOD_OVERRIDE'] = 'PUT';
        $_POST['data'] = 'update datum';

        $this->router->direct($this->container, '/');

        $this->expectOutputString(json_encode([
            'update datum'
        ], JSON_THROW_ON_ERROR, 512));
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function rest_delete_route_can_inject_parameters_and_output_content(): void
    {
        $this->router->rest([
            '/' => Rest::class
        ]);
        $_POST['REQUEST_METHOD_OVERRIDE'] = 'DELETE';
        $_POST['data'] = 'delete datum';

        $this->router->direct($this->container, '/');

        $this->expectOutputString(json_encode([
            'delete datum'
        ], JSON_THROW_ON_ERROR, 512));
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function rest_options_route_can_inject_parameters_and_output_content(): void
    {
        $this->router->rest([
            '/' => Rest::class
        ]);
        $_POST['REQUEST_METHOD_OVERRIDE'] = 'OPTIONS';
        $_POST['data'] = 'option';

        $this->router->direct($this->container, '/');

        $this->expectOutputString(json_encode([
            'option'
        ], JSON_THROW_ON_ERROR, 512));
    }

    /** @test */
    public function can_set_middleware_on_a_route(): void
    {
        $this->router->get(
            [
                '/' => static function () {
                    echo ' controller second';
                }
            ]
        )->middleware(Middleware::class);

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->router->direct($this->container, '/');

        $this->expectOutputString('middleware first controller second');
    }

    /** @test */
    public function middleware_only_runs_on_specified_route_type(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->router->get(
            [
                '/' => static function () {
                    echo 'get';
                }
            ]
        )->middleware(Middleware::class);

        $this->router->direct($this->container, '/');

        $this->expectOutputString('Not Found');
    }

    /** @test */
    public function more_than_one_middleware_can_be_registered_on_a_route(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->router->get(
            [
                '/' => static function () {
                    echo ' controller last';
                }
            ]
        )->middleware(Middleware::class)
            ->middleware(SecondMiddleware::class);

        $this->router->direct($this->container, '/');

        $this->expectOutputString('middleware first second middleware controller last');
    }
}
