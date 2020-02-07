<?php

namespace AegisFang\Router;

use AegisFang\Container\Container;
use AegisFang\Http\Error\NotFound;
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
     * @param string $file
     *
     * @return Router
     */
    public static function load(string $file): Router
    {
        $route = new static();
        require $file;
        return $route;
    }

    /**
     * @param array $route
     */
    public function get(array $route): void
    {
        $this->define($route, 'GET');
    }

    /**
     * @param array $route
     */
    public function post(array $route): void
    {
        $this->define($route, 'POST');
    }

    /**
     * @param array $route
     */
    public function put(array $route): void
    {
        $this->define($route, 'PUT');
    }

    /**
     * @param array $route
     */
    public function patch(array $route): void
    {
        $this->define($route, 'PATCH');
    }

    /**
     * @param array $route
     */
    public function delete(array $route): void
    {
        $this->define($route, 'DELETE');
    }

    /**
     * @param array $route
     */
    public function options(array $route): void
    {
        $this->define($route, 'OPTIONS');
    }

    /**
     * @param array $route
     */
    public function any(array $route): void
    {
        $this->define($route, 'ANY');
    }

    /**
     * @param array $routes
     * @param string $type
     */
    public function define(array $routes, string $type): void
    {
        $this->normalizeRoutes();

        foreach ($routes as $route => $controller) {
            if (isset($this->routes[$route])) {
                $this->routes[$route][$type] = $controller;
                continue;
            }

            $this->routes[$route] = [$type => $controller];
        }
    }

    /**
     * @param Container $container
     * @param string $uri
     *
     * @return Router
     */
    public function direct(Container $container, string $uri): self
    {
        $this->container = $container;

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
     * @param string $uri
     *
     * @return mixed
     * @throws ContainerException
     */
    protected function callClass(string $uri)
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
     * @param string $uri
     *
     * @return array
     */
    protected function getRouteCall(string $uri): array
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
     * @param string $uri
     *
     * @return string
     */
    protected function normalizeUri(string $uri): string
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
