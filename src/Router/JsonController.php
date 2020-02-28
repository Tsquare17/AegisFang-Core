<?php

namespace AegisFang\Router;

use AegisFang\Http\JsonResponse;
use AegisFang\Http\Request;

/**
 * Class JsonController
 * @package AegisFang\Router
 */
abstract class JsonController extends Controller
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
     */
    public function send(...$args): void
    {
        $args = $this->unsetGuarded($args);

        $this->response->make(
            ...$args
        )->send();
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
