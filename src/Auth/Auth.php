<?php

namespace AegisFang\Auth;

use AegisFang\Database\Query;
use AegisFang\Http\Request;

class Auth
{
    protected Request $request;
    protected Query $query;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->query = new Query();
    }

    public function register($name, $email, $pass): bool
    {
        return $this->query->insert(
            ['user_name', 'user_email', 'user_password'],
            [$name, $email, $pass]
        )->into('users')
         ->execute();
    }

    public function logIn(string $email, string $password): bool
    {
        return false;
    }

    public function logout(): void
    {
    }

    public static function check(): bool
    {
        return false;
    }
}
