<?php

namespace AegisFang\Tests\Fixtures;

use AegisFang\Controller\ApiController;

class Rest extends ApiController
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
