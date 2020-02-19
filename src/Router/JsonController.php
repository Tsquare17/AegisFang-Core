<?php

namespace AegisFang\Router;

use AegisFang\Http\JsonResponse;

abstract class JsonController extends Controller
{
    protected JsonResponse $response;
    protected array $guarded = [];

    public function __construct(JsonResponse $response)
    {
        $this->response = $response;
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
