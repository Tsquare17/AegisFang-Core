<?php

namespace AegisFang\Router;

use AegisFang\Container\Container;
use AegisFang\Http\Error\NotFound;
use AegisFang\Http\Response;
use Closure;
use Exception;
use AegisFang\Container\Exceptions\ContainerException;
use RuntimeException;

/**
 * Class Router
 * @package AegisFang\Router
 */
class Router
{

    /*
     * @var $routes
     */
    protected array $routes = [];

    /*
     * @var $container
     */
    protected Container $container;

    /*
     * @var $content
     */
    protected $content;

    /**
     * @param $file
     *
     * @return Router
     */
    public static function load($file): Router
    {
        $route = new static();
        require $file;
        return $route;
    }

    /**
     * @param $route
     */
    public function get($route): void
    {
        $this->define($route, 'GET');
    }

    /**
     * @param $route
     */
    public function post($route): void
    {
        $this->define($route, 'POST');
    }

    /**
     * @param $route
     */
    public function put($route): void
    {
        $this->define($route, 'PUT');
    }

    /**
     * @param $route
     */
    public function patch($route): void
    {
        $this->define($route, 'PATCH');
    }

    /**
     * @param $route
     */
    public function delete($route): void
    {
        $this->define($route, 'DELETE');
    }

    /**
     * @param $route
     */
    public function options($route): void
    {
        $this->define($route, 'OPTIONS');
    }

    /**
     * @param $route
     */
    public function any($route): void
    {
        $this->define($route, 'ANY');
    }

    /**
     * @param $routes
     * @param $type
     */
    public function define($routes, $type): void
    {
        $this->normalizeRoutes();

        foreach ($routes as $route => $controller) {
            if (isset($this->routes[$route])) {
                $this->routes[$route][$type] = $controller;
                continue;
            }
            $this->routes = array_merge($this->routes, [$route => [$type => $controller]]);
        }
    }

    /**
     * @param $container
     * @param $uri
     *
     * @return string
     */
    public function direct($container, $uri)
    {
        $this->container = $container;
        $this->container->set(self::class, $this);

        try {
            $uri = $this->normalizeUri($uri);
            if (array_key_exists($uri, $this->routes)) {
                if (
                    isset($this->routes[$uri][Request::method()])
                    &&
                    $this->routes[$uri][Request::method()] instanceof Closure
                ) {
                    $this->content = $this->container->injectClosure($this->routes[$uri][Request::method()]);

                    return $this;
                }

                if (isset($this->routes[$uri]['ANY']) && $this->routes[$uri]['ANY'] instanceof Closure) {
                    $this->content = $this->container->injectClosure($this->routes[$uri]['ANY']);

                    return $this;
                }

                $this->content = $this->callClass($uri);

                return $this;
            }
            throw new RuntimeException('No route defined for this URI.');
        } catch (Exception $e) {
            $response = new NotFound();
            $response->send();

            return $this;
        }
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param $uri
     *
     * @return mixed
     * @throws ContainerException
     */
    protected function callClass($uri)
    {
        // Need to check if route is wildcard and register default CRUD methods.
        try {
            [$class, $method] = $this->getRouteCall($uri);
            if (strpos($class, '\\') === false) {
                $class = 'App\\Controllers\\' . $class;
            }

            $call = $this->container->get($class);

            return $this->callMethod($call, $method);
        } catch (Exception $e) {
            throw new ContainerException($this->routes[$uri][Request::method()] . ' not found');
        }
    }

    protected function callMethod($class, $method)
    {
        return $this->container->getMethod($class, $method);
    }

    /**
     * @param $uri
     *
     * @return array
     */
    protected function getRouteCall($uri): array
    {
        return explode('::', $this->routes[$uri][Request::method()]);
    }

    /**
     * Make sure all routes begin with /
     *
     * @return void
     */
    protected function normalizeRoutes(): void
    {
        $normalizedRoutes = [];
        foreach ($this->routes as $route => $direction) {
            if ($route === '' || strpos($route, '/') !== 0) {
                $normalizedRoutes['/' . $route] = $direction;
                continue;
            }
            $normalizedRoutes[$route] = $direction;
        }
        $this->routes = $normalizedRoutes;
    }

    /**
     * Make sure URI begins with /
     *
     * @param $uri
     *
     * @return string
     */
    protected function normalizeUri($uri): string
    {
        if ($uri === '' || strpos($uri, '/') !== 0) {
            return '/' . $uri;
        }
        return $uri;
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
