<?php

namespace AegisFang\Http\Error;

use AegisFang\Http\Response;

/**
 * Class NotFound
 * @package AegisFang\Http\Error
 */
class NotFound extends Response
{
    /**
     * NotFound constructor.
     */
    public function __construct()
    {
        $this->setStatusCode(404);
        $this->setBody('Not Found');
    }

    /**
     * Send the response.
     */
    public function send()
    {
        return $this->body();
    }
}
