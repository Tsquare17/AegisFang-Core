<?php

namespace AegisFang\Router;

use AegisFang\Http\JsonResponse;
use AegisFang\Http\Request;

/**
 * Class JsonRestController
 * @package AegisFang\Router
 */
class JsonRestController extends JsonController
{
    protected JsonResponse $response;
    protected Router $router;

    /**
     * JsonRestController constructor.
     *
     * @param JsonResponse $response
     * @param Request      $request
     * @param Router       $router
     */
    public function __construct(JsonResponse $response, Request $request, Router $router)
    {
        parent::__construct($response, $request);
        $this->router = $router;
    }

    /**
     * Send the response.
     *
     * @param mixed ...$args
     */
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
