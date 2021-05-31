<?php

namespace AegisFang\Controller;

use AegisFang\Http\JsonResponse;
use AegisFang\Http\Request;
use AegisFang\Router\Router;

/**
 * Class JsonRestController
 * @package AegisFang\Controller
 */
class RestController extends ApiController
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
     *
     * @throws \JsonException
     */
    public function dispatch(...$args): string
    {
        $args = $this->unsetGuarded($args);

        return $this->response->make(
            [
                'routes' => $this->router->getRoutes(),
                'data' => $args
            ]
        )->dispatch();
    }
}
