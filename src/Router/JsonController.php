<?php

namespace AegisFang\Router;

use AegisFang\Http\JsonResponse;
use AegisFang\Http\Request;

abstract class JsonController extends Controller
{
    protected JsonResponse $response;
    protected Request $request;

    public function __construct(JsonResponse $response, Request $request)
    {
        $this->response = $response;
        $this->request = $request;
    }

    public function send(...$args): void
    {
        $this->response->make(
            ...$args
        )->send();
    }
}
