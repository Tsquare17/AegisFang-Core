<?php

namespace AegisFang\Auth;

use AegisFang\Auth\Exceptions\AuthException;

class AuthGuard
{
    public function __construct(Auth $auth)
    {
        if (!Auth::check()) {
            // TODO: Redirect to 403
        }
    }
}
