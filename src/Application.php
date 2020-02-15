<?php

namespace AegisFang;

use Dotenv\Dotenv;
use AegisFang\Container\Container;
use AegisFang\Router\Router;

/**
 * Class Application
 * @package AegisFang\Core
 */
class Application extends Container
{
    protected Container $container;

    protected string $basePath;

    protected Dotenv $config;


    public function __construct(string $basePath = null)
    {
        $this->basePath = $basePath ?: $this->setBasePath();

        $this->registerBindings();
    }

    public function setBasePath(): string
    {
        return dirname(__DIR__, 3) . '/';
    }

    public function getBasePath(): string
    {
        return $this->basePath;
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
        $router = Router::load('../config/routes.php');
        $this->set(Router::class, $router);

        $router->direct($this);
    }
}
