<?php

namespace AegisFang\Tests;

use PHPUnit\Framework\TestCase;
use AegisFang\Http\Request;

class RequestTest extends TestCase
{
    protected Request $request;

    public function setUp(): void
    {
        $_GET = [
            'id' => '1',
            'var' => 'foo'
        ];

        $_POST = [
            'id' => '2',
            'var' => 'bar'
        ];

        $this->request = new Request();
    }

    /** @test */
    public function slashes_are_trimmed_from_request_uri(): void
    {
        $_SERVER['REQUEST_URI'] = '/page//';

        $this->assertEquals('page', Request::uri());
    }

    /** @test */
    public function can_override_request_method_using_post_param(): void
    {
        $_POST['REQUEST_METHOD_OVERRIDE'] = 'PUT';

        $this->assertEquals(Request::method(), 'PUT');
    }

    /** @test */
    public function can_collect_get_parameters(): void
    {
        $this->assertEquals(
            $this->request->getParams(),
            [
                'id' => '1',
                'var' => 'foo'
            ]
        );
    }

    /** @test */
    public function can_collect_post_parameters(): void
    {
        $this->assertEquals(
            $this->request->postParams(),
            [
                'id' => '2',
                'var' => 'bar'
            ]
        );
    }
}
