<?php

namespace AegisFang\Tests;

use AegisFang\Container\Container;
use AegisFang\Router\Router;
use AegisFang\View\View;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    protected Router $router;

    protected Container $container;

    public function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->router = Router::load(__DIR__ . '/../Fixtures/routes.php');
        $this->container = new Container();
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function api_controller_outputs_json(): void
    {
        $data = [
            'names' => [
                'bob',
                'tom',
                'joe',
            ]
        ];

        $this->router->get([
            '/' => '\AegisFang\Tests\Fixtures\Json::index'
        ]);

        echo $this->router->direct($this->container, '/');

        $this->expectOutputString(json_encode($data));
    }

    /** @test */
    public function base_controller_outputs_views(): void
    {
        $this->router->get([
            '/' => '\AegisFang\Tests\Fixtures\Controller::index'
        ]);

        $content = $this->router->direct($this->container, '/');

        $this->assertEquals('headingcontentcopyright', $content);
    }
}
