<?php

namespace AegisFang\Router;

use AegisFang\Http\JsonResponse;

class JsonRestController
{
    protected JsonResponse $response;
    protected Router $router;

    public function __construct(JsonResponse $response, Router $router)
    {
        $this->response = $response;
        $this->router = $router;
    }

    public function send(...$args): void
    {
        $return = [
            'routes' => $this->router->getRoutes(),
            'data' => $args
        ];

        $this->response->make(
            $return
        )->send();
    }
}
