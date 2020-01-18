<?php

namespace AegisFang\Router;

use AegisFang\Http\JsonResponse;
use AegisFang\Http\Response;

abstract class RestController extends Controller
{
    protected $response;

    public function __construct(JsonResponse $response)
    {
        $this->response = $response;
    }

    public function send(...$args)
    {
        return $this->response->make(
            ...$args
        )->send();
    }
}
