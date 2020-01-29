<?php

namespace AegisFang\Router;

use AegisFang\Http\JsonResponse;

abstract class JsonController extends Controller
{
    protected JsonResponse $response;

    public function __construct(JsonResponse $response)
    {
        $this->response = $response;
    }

    public function send(...$args): void
    {
        $this->response->make(
            ...$args
        )->send();
    }
}
