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

    /**
     * Application constructor.
     *
     * @param string|null $basePath
     */
    public function __construct(string $basePath = null)
    {
        $this->basePath = $basePath ?: $this->defaultBasePath();

        $this->registerBindings();

        parent::__construct();
    }

    /**
     * @return string
     */
    public function defaultBasePath(): string
    {
        return dirname(__DIR__, 3) . '/';
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Set container instance and load env config.
     */
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
