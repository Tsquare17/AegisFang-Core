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
            'var' => 'foo',
            'get' => 'got',
        ];

        $_POST = [
            'id' => '2',
            'var' => 'bar',
            'post' => 'posted',
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

        $this->assertEquals('PUT', Request::method());
    }

    /** @test */
    public function can_collect_get_parameters(): void
    {
        $this->assertEquals(
            [
                'id' => '1',
                'var' => 'foo',
                'get' => 'got',
            ],
            $this->request->getParams()
        );
    }

    /** @test */
    public function can_collect_post_parameters(): void
    {
        $this->assertEquals(
            [
                'id' => '2',
                'var' => 'bar',
                'post' => 'posted',
            ],
            $this->request->postParams()
        );
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function can_access_request_get_parameter_via_magic_method(): void
    {
        $this->assertEquals(
            'got',
            $this->request->get
        );
    }

    /** @test */
    public function can_access_request_post_parameter_via_magick_method(): void
    {
        $this->assertEquals(
            'posted',
            $this->request->post
        );
    }
}
