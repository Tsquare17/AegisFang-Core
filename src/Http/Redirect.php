<?php

namespace AegisFang\Http;

class Redirect
{
    public static function to(string $url): void
    {
        header('Location: ' . $url);
        die();
    }
}
