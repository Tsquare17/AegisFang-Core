<?php

namespace AegisFang\Tests\Fixtures;

use AegisFang\Router\JsonController;

class Json extends JsonController
{
    public function index(): void
    {
        $data = [
            'names' => [
                'bob',
                'tom',
                'joe',
            ]
        ];

        $this->send(
            $data
        );
    }
}
