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
        $hashingAlgorithm = getenv('hashing_algorithm') ?: PASSWORD_BCRYPT;

        $hashedPass = password_hash($pass, $hashingAlgorithm);

        return $this->query->insert(
            ['user_name', 'user_email', 'user_password'],
            [$name, $email, $hashedPass]
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
     * @param $userName
     * @param $password
     *
     * @return bool
     */
    public static function check($userName, $password): bool
    {
        $query = new Query();
        $user = $query->select('*')
            ->from('users')
            ->where('user_name', $userName)
            ->execute()
            ->fetch();

        if (password_verify($password, $user['user_password'])) {
            return true;
        }

        return false;
    }
}
