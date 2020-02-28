<?php

namespace AegisFang\Auth;

use AegisFang\Http\Error\Forbidden;

/**
 * Class AuthGuard
 * @package AegisFang\Auth
 */
class AuthGuard
{
    /**
     * AuthGuard constructor.
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        if (!Auth::check()) {
            $response = new Forbidden();
            $response->send();
        }
    }
}
