<?php

namespace AegisFang\Auth;

use AegisFang\Http\Error\Forbidden;
use AegisFang\Http\Redirect;

class AuthGuard
{
    public function __construct(Auth $auth)
    {
        if (!Auth::check()) {
            $response = new Forbidden();
            $response->send();
        }
    }
}
