<?php

namespace AegisFang\Router;

use Closure;
use Exception;
use AegisFang\Container\Exceptions\ContainerException;


class Router {

	protected $routes = [];
	protected const REQUESTTYPEKEY = 0;
	protected const METHODKEY = 1;


	public static function load($file)
	{
		$route = new static;
		require $file;
		return $route;
	}

	public function get($route)
	{
		$this->define($route, 'GET');
	}

	public function define($route, $type)
	{
		$key = array_keys($route)[0];
		$val = array_values($route)[0];
		$route = [$key => [$type, $val]];
		$this->routes = array_merge($this->routes, $route);
		$this->normalizeRoutes();
	}

	public function direct($container, $uri)
	{
		try {
			$uri = $this->normalizeUri($uri);
			if (array_key_exists($uri, $this->routes)) {
				if($this->routes[$uri][self::METHODKEY] instanceof Closure) {
					return $this->routes[$uri][self::METHODKEY]();
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
			if($_SERVER['REQUEST_METHOD'] !== $this->routes[$uri][self::REQUESTTYPEKEY]) {
				return '404';
			}

			[$class, $method] = $this->getRouteCall($uri);

			return [$container->get('App\\Controllers\\' . $class), $method];
		} catch (Exception $e) {
			throw new ContainerException($this->routes[$uri][self::METHODKEY] . ' not found');
		}
	}

	protected function getRouteCall($uri): array {
		return explode('::', $this->routes[$uri][self::METHODKEY]);
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
	 * @return string|null
	 */
	protected function normalizeUri( $uri ):?string
	{
		if($uri === '' || strpos($uri, '/') !== 0) {
			return '/' . $uri;
		}
		return $uri;
	}
}
