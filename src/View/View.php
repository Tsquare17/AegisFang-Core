<?php

namespace AegisFang\View;

use Dotenv\Exception\InvalidPathException;

/**
 * Class View
 * @package AegisFang\View
 */
class View
{
    /**
     * Path to the PHP template files.
     */
    protected string $templatesPath;

    protected array $data = [];

    public function __construct(string $template, array $data = [])
    {
        $basePath = getenv('APP_PATH')
            ? getenv('APP_PATH') . 'resources/views/'
            : getcwd() . '/resources/views/';

        if (getenv('RUNNING_TESTS')) {
            $basePath = getcwd() . '/tests/Fixtures/resources/views/';
        }

        if (!is_dir($basePath)) {
            throw new InvalidPathException('Templates path not found');
        }

        $this->data = $data;

        $this->templatesPath = $basePath . $template . '.php';
    }

    public function __toString(): string
    {
        if (!function_exists('AegisFang\View\render')) {
            function render($template, $data = [])
            {
                extract($data, EXTR_OVERWRITE);
                unset($data);

                ob_start();

                include $template;

                return ob_get_clean();
            }
        };

        return render($this->templatesPath, $this->data);
    }
}
