<?php

namespace AegisFang\Router;

use AegisFang\Http\JsonResponse;
use AegisFang\Http\Request;

abstract class JsonController extends Controller
{
    protected JsonResponse $response;
    protected Request $request;
    protected array $guarded = [];

    public function __construct(JsonResponse $response, Request $request)
    {
        $this->response = $response;
        $this->request  = $request;
    }

    public function send(...$args): void
    {
        $args = $this->unsetGuarded($args);

        $this->response->make(
            ...$args
        )->send();
    }

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
