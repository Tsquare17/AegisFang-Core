<?php

namespace AegisFang\Tests\Fixtures;

use AegisFang\Router\JsonController;

class Rest extends JsonController
{
    public function index(): void
    {
        $this->send(
            [
                'index'
            ]
        );
    }

    public function get($id, $name): void
    {
        $this->send(
            [
                $id => $name
            ]
        );
    }

    public function post($data): void
    {
        $this->send(
            [
                $data
            ]
        );
    }

    public function put($data): void
    {
        $this->send(
            [
                $data
            ]
        );
    }

    public function delete($data): void
    {
        $this->send(
            [
                $data
            ]
        );
    }

    public function options($data): void
    {
        $this->send(
            [
                $data
            ]
        );
    }
}
