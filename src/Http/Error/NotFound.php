<?php

namespace AegisFang\Http\Error;

use AegisFang\Http\Response;

class NotFound extends Response
{
    public function __construct()
    {
        $this->setStatusCode(404);
        $this->setBody('Not Found');
    }

    public function send(): void
    {
        echo $this->body();
    }
}