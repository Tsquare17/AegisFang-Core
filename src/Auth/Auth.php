<?php

namespace AegisFang\Auth;

use AegisFang\Database\Query;
use AegisFang\Http\Request;

/**
 * Class Auth
 * @package AegisFang\Auth
 */
class Auth
{
    protected Request $request;
    protected Query $query;

    /**
     * Auth constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->query = new Query();
    }

    /**
     * @param $name
     * @param $email
     * @param $pass
     *
     * @return bool
     */
    public function register($name, $email, $pass): bool
    {
        return $this->query->insert(
            ['user_name', 'user_email', 'user_password'],
            [$name, $email, $pass]
        )->into('users')
         ->execute();
    }

    /**
     * Log in.
     *
     * @param string $email
     * @param string $password
     *
     * @return bool
     */
    public function logIn(string $email, string $password): bool
    {
        return false;
    }

    /**
     * Log out.
     */
    public function logout(): void
    {
    }

    /**
     * Check authentication.
     *
     * @return bool
     */
    public static function check(): bool
    {
        return false;
    }
}
