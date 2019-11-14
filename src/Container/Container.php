<?php

namespace AegisFang\Container;

use Psr\Container\ContainerInterface;
use AegisFang\Container\Exceptions\NotFoundException;
use AegisFang\Container\Exceptions\ContainerException;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;


/**
 * Class Container
 * @package AegisFang\Container
 */
class Container implements ContainerInterface {

	protected static $instance;

	protected $services = [];

	/**
	 * Finds an entry of the container by its identifier and returns it.
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @return mixed Entry.
	 * @throws ContainerException Error while retrieving the entry.
	 *
	 * @throws NotFoundException  No entry was found for **this** identifier.
	 */
	public function get( $id ) {
		$entry = $this->resolve($id);
		if(!($entry instanceof ReflectionClass)) {
			return $entry;
		}

		return $this->getInstance($entry);
	}

	/**
	 * Returns true if the container can return an entry for the given identifier.
	 * Returns false otherwise.
	 *
	 * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
	 * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @return bool
	 */
	public function has( $id ): bool {
		try {
			$item = $this->resolve($id);
		} catch (NotFoundException $e) {
			return false;
		}

		return $item->isInstantiable();
	}

	/**
	 * Set a dependency and return the instance of the container.
	 *
	 * @param string $key
	 * @param $value
	 *
	 * @return $this
	 */
	public function set(string $key, $value): self {
		$this->services[$key] = $value;

		return $this;
	}

	/**
	 * @param $id
	 *
	 * @return ReflectionClass|null
	 */
	public function resolve($id): ?ReflectionClass {
		try {
			$name = $id;
			if (isset($this->services[$id])) {
				$name = $this->services[$id];
				if (is_callable($name)) {
					return $name();
				}
			}
			return (new ReflectionClass($name));
		} catch (ReflectionException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * @param ReflectionClass $item
	 *
	 * @return object
	 * @throws ContainerException
	 * @throws NotFoundException
	 */
	public function getInstance(ReflectionClass $item)
	{
		$constructor = $item->getConstructor();
		if ( $constructor === null || $constructor->getNumberOfRequiredParameters() === 0) {
			return $item->newInstance();
		}
		$params = [];
		foreach ($constructor->getParameters() as $param) {
			if ($type = $param->getType()) {
				$params[] = $this->get($type->getName());
			}
		}

		return $item->newInstanceArgs($params);
	}

	public function getMethod( $class, $method )
	{
		$reflector = new ReflectionClass($class);
		// this is how we get some method arguments
		$params = $reflector->getMethod($method)->getParameters();

		$args = [];
		foreach($params as $param) {
			$args[] = $this->getParameter($class, $method, $param);
		}

		$resolved = [];
		foreach($args as $arg) {
			$resolved[] = $this->get($arg);
		}

		return call_user_func_array([$class, $method], $resolved);
	}

	public function getParameter( $class, $method, $arg )
	{
		$parameter = new ReflectionParameter([$class, $method], $arg->name);

		$type = $parameter->getType();

		return $type->getName();
	}

	public static function setInstance(Container $container = null): Container {
		return static::$instance = $container;
	}
}
