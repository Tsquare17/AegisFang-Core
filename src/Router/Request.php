<?php

namespace AegisFang\Router;

/**
 * Class Request
 * @package AegisFang\Router
 */
class Request
{
    public static function uri(): string
    {
        return trim($_SERVER['REQUEST_URI'], '/');
    }

    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}
