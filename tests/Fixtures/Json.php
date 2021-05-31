<?php

namespace AegisFang\Tests\Fixtures;

use AegisFang\Controller\ApiController;

class Json extends ApiController
{
    public function index()
    {
        $data = [
            'names' => [
                'bob',
                'tom',
                'joe',
            ]
        ];

        return $this->dispatch(
            $data
        );
    }

    public function getUrlParam($id, $name)
    {
        return $this->dispatch(
            [
                $id => $name,
            ]
        );
    }
}
