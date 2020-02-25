<?php

namespace AegisFang\Http\Error;

use AegisFang\Http\Response;

class Forbidden extends Response
{
    public function __construct()
    {
        $this->setStatusCode(403);
        $this->setBody('Forbidden');
    }

    public function send(): void
    {
        echo $this->body();
    }
}
