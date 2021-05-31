<?php

namespace AegisFang\Controller;

use AegisFang\View\View;

/**
 * Class BaseController
 * @package AegisFang\Controller
 */
abstract class BaseController
{
    public function dispatch($content): string
    {
        return new View('index', ['content' => $content]);
    }
}
