<?php

namespace AegisFang\Router;

use AegisFang\Container\Container;
use AegisFang\Container\Exceptions\NotFoundException;
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
     * @return Router
     */
    public static function load(string $file): Router
    {
        $route = new static();
        $route->logger = Logger::getLogger();
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

        if (isset($routes[0])) {
            $this->collectRestfulMiddleware($middleware);

            return $this;
        }

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
                    if (isset($this->routes[$route][$method])) {
                        $this->logger->notice(
                            'Multiple route definitions.',
                            ['route' => $this->routes[$route][$method]]
                        );
                    }

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
     * @throws ContainerException
     * @throws NotFoundException
     * @throws \ReflectionException
     */
    public function direct(Container $container, string $uri = null): self
    {
        $this->container = $container;

        $this->request = new Request();

        $this->container->set(Request::class, $this->request);

        $uri = $uri ?: Request::uri();
        $uri = $this->normalizeUri($uri);

        if (array_key_exists($uri, $this->routes)) {
            $this->runMiddleware($uri);

            if (
                isset($this->routes[$uri][Request::method()])
                &&
                $this->routes[$uri][Request::method()] instanceof Closure
            ) {
                $this->content = $this->container->injectClosure($this->routes[$uri][Request::method()]);

                return $this;
            }

            if (isset($this->routes[$uri][Request::method()])) {
                $this->content = $this->callClass($uri);

                return $this;
            }
        }

        $this->logger->warning(
            'Failed to match route.',
            [
                'route' => Request::method() . ' ' . $uri,
            ]
        );

        $response = new NotFound();
        $response->send();

        return $this;
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
            $this->logger->critical(
                'Failed to call route controller',
                ['exception' => $e->getMessage()]
            );
        }
    }

    protected function callMethod($class, $method)
    {
        try {
            return $this->container->getMethod($class, $method);
        } catch (ContainerException $e) {
            $this->logger->critical(
                'Failed to get controller from container.',
                ['exception' => $e->getMessage()]
            );
        } catch (NotFoundException $e) {
            $this->logger->warning(
                'Not found.',
                ['exception' => $e->getMessage()]
            );
        } catch (\ReflectionException $e) {
            $this->logger->warning(
                'Reflection exception.',
                ['exception' => $e->getMessage()]
            );
        }
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
    public function runMiddleware(string $uri): void
    {
        if (
            array_key_exists($uri, $this->middleware)
            && isset($this->middleware[$uri][Request::method()])
            && $this->middleware[$uri][Request::method()] !== null
        ) {
            foreach ($this->middleware[$uri][Request::method()] as $middleware) {
                try {
                    $this->container->get($middleware);
                } catch (NotFoundException $e) {
                    $this->logger->warning(
                        'Middleware not found.',
                        ['exception' => $e->getMessage()]
                    );
                } catch (ContainerException $e) {
                    $this->logger->warning(
                        'Container exception occurred while getting middleware.',
                        ['exception' => $e->getMessage()]
                    );
                }
            }
        }
    }

    /**
     * Collect middleware specified on REST routes.
     *
     * @param string $middleware
     *
     * @return Router
     */
    public function collectRestfulMiddleware(string $middleware): Router
    {
        $routes = $this->lastRegisteredRoutes;

        foreach ($routes as $route) {
            if (isset($this->middleware[$route[0]][$route[1]])) {
                $this->middleware[$route[0]][$route[1]][] = $middleware;
                continue;
            }
            $this->middleware[$route[0]][$route[1]][] = $middleware;
        }

        return $this;
    }
}
