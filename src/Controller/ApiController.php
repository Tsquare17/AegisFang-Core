<?php

namespace AegisFang\Controller;

use AegisFang\Http\JsonResponse;
use AegisFang\Http\Request;

/**
 * Class JsonController
 * @package AegisFang\Controller
 */
abstract class ApiController
{
    protected JsonResponse $response;
    protected Request $request;
    protected array $guarded = [];

    /**
     * JsonController constructor.
     *
     * @param JsonResponse $response
     * @param Request      $request
     */
    public function __construct(JsonResponse $response, Request $request)
    {
        $this->response = $response;
        $this->request  = $request;
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
            ...$args
        )->dispatch();
    }

    /**
     * Unset guarded items from the response.
     *
     * @param $args
     *
     * @return array
     */
    protected function unsetGuarded($args): array
    {
        foreach ($args as $key => $val) {
            if (in_array($key, $this->guarded, true)) {
                unset($args[$key]);
                continue;
            }

            if (is_array($val)) {
                $args[$key] = $this->unsetGuarded($val);
            }
        }

        return $args;
    }
}
