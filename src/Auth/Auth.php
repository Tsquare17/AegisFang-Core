<?php

namespace AegisFang\Auth;

use AegisFang\Http\Request;

class Auth
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function logIn(): bool
    {
        return false;
    }

    public static function check(): bool
    {
        return false;
    }
}
