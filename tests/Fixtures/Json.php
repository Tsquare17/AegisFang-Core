<?php

namespace AegisFang\Tests\Fixtures;

use AegisFang\Controller\JsonController;

class Json extends JsonController
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
