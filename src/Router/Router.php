<?php

namespace AegisFang\Router;

use AegisFang\Container\Container;
use AegisFang\Http\Request;
use AegisFang\Http\Error\NotFound;
use AegisFang\Log\Logger;
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
    protected array $routes = [];

    protected array $lastRegisteredRoutes = [];

    protected Container $container;

    protected Request $request;

    protected Logger $logger;

    protected ?string $content;

    protected array $middleware = [];

    protected const REQUEST_METHODS = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];

    /**
     * @param string $file
     *
     * @param string $basePath
     *
     * @return Router
     */
    public static function load(string $file): Router
    {
        $route = new static();
        $route->logger = new Logger();
        require $file;
        return $route;
    }

    /**
     * @param array $route
     *
     * @return Router
     */
    public function get(array $route): Router
    {
        $this->define($route, 'GET');

        return $this;
    }

    /**
     * @param array $route
     *
     * @return Router
     */
    public function post(array $route): Router
    {
        $this->define($route, 'POST');

        return $this;
    }

    /**
     * @param array $route
     *
     * @return Router
     */
    public function put(array $route): Router
    {
        $this->define($route, 'PUT');

        return $this;
    }

    /**
     * @param array $route
     *
     * @return Router
     */
    public function delete(array $route): Router
    {
        $this->define($route, 'DELETE');

        return $this;
    }

    /**
     * @param array $route
     *
     * @return Router
     */
    public function options(array $route): Router
    {
        $this->define($route, 'OPTIONS');

        return $this;
    }

    /**
     * @param array $route
     *
     * @return Router
     */
    public function rest(array $route): Router
    {
        $this->defineRestRoutes($route);

        return $this;
    }

    /**
     * @param array  $routes
     * @param string $type
     */
    public function define(array $routes, string $type): void
    {
        $this->normalizeRoutes();

        $this->lastRegisteredRoutes = [$routes, $type];

        foreach ($routes as $route => $controller) {
            if (isset($this->routes[$route])) {
                if (isset($this->routes[$route][$type])) {
                    $this->logger->notice(
                        'Multiple route definitions.',
                        ['route' => $this->routes[$route][$type]]
                    );
                }

                $this->routes[$route][$type] = $controller;
                continue;
            }

            $this->routes[$route] = [$type => $controller];
        }
    }

    /**
     * Collect middleware.
     *
     * @param string $middleware
     *
     * @return Router
     */
    public function middleware(string $middleware): Router
    {
        [$routes, $type] = $this->lastRegisteredRoutes;
        foreach ($routes as $route => $controller) {
            if (isset($this->middleware[$route][$type])) {
                $this->middleware[$route][$type][] = $middleware;
                continue;
            }
            $this->middleware[$route] = [$type => [$middleware]];
        }

        return $this;
    }

    /**
     * @param array $routes
     */
    public function defineRestRoutes(array $routes): void
    {
        $this->normalizeRoutes();

        $this->lastRegisteredRoutes = [];

        foreach (self::REQUEST_METHODS as $method) {
            foreach ($routes as $route => $controller) {
                $this->lastRegisteredRoutes[] = [$route, $method];
                if (isset($this->routes[ $route ])) {
                    if (! $controller instanceof Closure) {
                        $this->routes[ $route ][ $method ] = $controller . '::' . strtolower($method);
                        continue;
                    }
                    $this->routes[ $route ][ $method ] = $controller;
                    continue;
                }

                if (! $controller instanceof Closure) {
                    $this->routes[ $route ] = [ $method => $controller . '::' . strtolower($method)];
                    continue;
                }

                $this->routes[ $route ] = [ $method => $controller ];
            }
        }
    }

    /**
     * @param Container   $container
     *
     * @param string|null $uri
     *
     * @return Router
     */
    public function direct(Container $container, string $uri = null): self
    {
        $this->container = $container;

        $this->request = new Request();

        $this->container->set(Request::class, $this->request);

        try {
            $uri = $uri ?: Request::uri();
            $uri = $this->normalizeUri($uri);

            if (array_key_exists($uri, $this->routes)) {
                $this->registerMiddleware($uri);

                if (
                    isset($this->routes[$uri][Request::method()])
                    &&
                    $this->routes[$uri][Request::method()] instanceof Closure
                ) {
                    $this->content = $this->container->injectClosure($this->routes[$uri][Request::method()]);

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
     * Get all registered routes.
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Register middleware for the route.
     *
     * @param string $uri
     */
    public function registerMiddleware(string $uri): void
    {
        if (
            array_key_exists($uri, $this->middleware)
            && isset($this->middleware[$uri][Request::method()])
            && $this->middleware[$uri][Request::method()] !== null
        ) {
            foreach ($this->middleware[$uri][Request::method()] as $middleware) {
                try {
                    $this->container->get($middleware);
                } catch (Exception $e) {
                }
            }
        }
    }
}
