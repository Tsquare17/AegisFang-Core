<?php

namespace AegisFang\Tests;

use PHPUnit\Framework\TestCase;
use AegisFang\Router\Request;

class RequestTest extends TestCase
{
    public function setUp(): void
    {
        $_SERVER['REQUEST_URI'] = '/page//';
    }

    /** @test */
    public function slashes_are_trimmed_from_request_uri(): void
    {
        $uri = Request::uri();
        $this->assertEquals('page', $uri);
    }
}
