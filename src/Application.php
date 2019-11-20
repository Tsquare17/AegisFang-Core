<?php

namespace AegisFang;

use Dotenv\Dotenv;
use AegisFang\Container\Container;
use AegisFang\Router\Router;
use AegisFang\Router\Request;

/**
 * Class Application
 * @package AegisFang\Core
 */
class Application extends Container
{
    protected $container;

    protected $basePath;

    protected $config;

    public function __construct($basePath = null)
    {
        $this->basePath = $basePath ?: $this->setBasePath();

        $this->registerBindings();
    }

    public function setBasePath(): string
    {
        return dirname(__DIR__, 3) . '/';
    }

    public function registerBindings(): void
    {
        static::setInstance($this);

        $this->set('App', $this);

        $this->config = Dotenv::create($this->basePath);
        $this->config->load();

        $this->set(Container::class, $this);
    }

    public function run()
    {
        return Router::load('../config/routes.php')
                     ->direct($this, Request::uri());
    }
}
