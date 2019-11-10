<?php

namespace AegisFang\Router;

use Closure;
use Exception;
use AegisFang\Container\Exceptions\ContainerException;


class Router {

	protected $routes = [];

	public static function load($file)
	{
		$route = new static;
		require $file;
		return $route;
	}

	public function get($route): void
	{
		$this->define($route, 'GET');
	}

	public function post($route): void
	{
		$this->define($route, 'POST');
	}

	public function put($route): void
	{
		$this->define($route, 'PUT');
	}

	public function patch($route): void
	{
		$this->define($route, 'PATCH');
	}

	public function delete($route): void
	{
		$this->define($route, 'DELETE');
	}

	public function define($route, $type): void
	{
		$this->normalizeRoutes();

		$key = array_keys($route)[0];
		$val = array_values($route)[0];
		$route = [$key => [$type => $val]];
		if(isset($this->routes[$key])) {
			$this->routes[$key][$type] = $val;
		} else {
			$this->routes = array_merge($this->routes, $route);
		}
	}

	public function direct($container, $uri)
	{
		try {
			$uri = $this->normalizeUri($uri);
			if (array_key_exists($uri, $this->routes)) {
				if($this->routes[$uri][Request::method()] instanceof Closure) {
					return $this->routes[$uri][Request::method()]();
				}

				return $this->callClass($container, $uri);
			}
			throw new Exception('No route defined for this URI.');
		} catch(Exception $e) {
			return '404';
		}
	}

	protected function callClass($container, $uri): ?array {
		try {
			[$class, $method] = $this->getRouteCall($uri);

			return [$container->get('App\\Controllers\\' . $class), $method];
		} catch (Exception $e) {
			throw new ContainerException($this->routes[$uri][Request::method()] . ' not found');
		}
	}

	protected function getRouteCall($uri): array {
		return explode('::', $this->routes[$uri][Request::method()]);
	}

	/**
	 * Make sure all routes begin with /
	 *
	 * @return null
	 */
	protected function normalizeRoutes(): void {
		$normalizedRoutes = [];
		foreach($this->routes as $route => $direction) {
			if($route === '' || strpos($route, '/') !== 0) {
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
	protected function normalizeUri( $uri ): string
	{
		if($uri === '' || strpos($uri, '/') !== 0) {
			return '/' . $uri;
		}
		return $uri;
	}
}
