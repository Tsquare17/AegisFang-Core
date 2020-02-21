<?php

namespace AegisFang\Router;

use AegisFang\Http\JsonResponse;
use AegisFang\Http\Request;

class JsonRestController extends JsonController
{
    protected JsonResponse $response;
    protected Router $router;

    public function __construct(JsonResponse $response, Request $request, Router $router)
    {
        parent::__construct($response, $request);
        $this->router = $router;
    }

    public function send(...$args): void
    {
        $args = $this->unsetGuarded($args);

        $this->response->make(
            [
                'routes' => $this->router->getRoutes(),
                'data' => $args
            ]
        )->send();
    }
}
