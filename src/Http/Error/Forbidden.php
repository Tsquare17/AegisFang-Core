<?php

namespace AegisFang\Http\Error;

use AegisFang\Http\Response;

/**
 * Class Forbidden
 * @package AegisFang\Http\Error
 */
class Forbidden extends Response
{
    /**
     * Forbidden constructor.
     */
    public function __construct()
    {
        $this->setStatusCode(403);
        $this->setBody('Forbidden');
    }

    /**
     * Send the response.
     */
    public function send(): void
    {
        echo $this->body();
    }
}
