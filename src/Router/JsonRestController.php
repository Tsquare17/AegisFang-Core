<?php

namespace AegisFang\Router;

use AegisFang\Http\JsonResponse;

class JsonRestController extends JsonController
{
    protected JsonResponse $response;
    protected Router $router;

    public function __construct(JsonResponse $response, Router $router)
    {
        parent::__construct($response);
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
