<?php

namespace AegisFang\Tests\Fixtures;

use AegisFang\Controller\BaseController;
use AegisFang\View\View;

class Controller extends BaseController
{
    public function index()
    {
        $header = new View('header', [
            'content' => 'heading',
        ]);

        $footer = new View('footer', [
            'content' => 'copyright'
        ]);

        $content = new View('content', [
                'header' => $header,
                'footer' => $footer,
        ]);

        return $this->dispatch($content);
    }
}