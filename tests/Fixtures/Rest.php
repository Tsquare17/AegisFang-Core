<?php

namespace AegisFang\Tests\Fixtures;

use AegisFang\Router\JsonController;

class Rest extends JsonController
{
    public function index()
    {
        return $this->dispatch(
            [
                'index'
            ]
        );
    }

    public function get($id, $name)
    {
        return $this->dispatch(
            [
                $id => $name
            ]
        );
    }

    public function post($data)
    {
        return $this->dispatch(
            [
                $data
            ]
        );
    }

    public function put($data)
    {
        return $this->dispatch(
            [
                $data
            ]
        );
    }

    public function delete($data)
    {
        return $this->dispatch(
            [
                $data
            ]
        );
    }

    public function options($data)
    {
        return $this->dispatch(
            [
                $data
            ]
        );
    }
}
