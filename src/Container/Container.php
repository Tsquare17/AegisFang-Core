<?php

namespace AegisFang\Container;

use AegisFang\Http\Request;
use AegisFang\Log\Logger;
use Closure;
use Psr\Container\ContainerInterface;
use AegisFang\Container\Exceptions\NotFoundException;
use AegisFang\Container\Exceptions\ContainerException;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionParameter;

/**
 * Class Container
 * @package AegisFang\Container
 */
class Container implements ContainerInterface
{
    protected static Container $instance;

    protected Logger $logger;

    protected array $services = [];

    /**
     * Container constructor.
     */
    public function __construct()
    {
        $this->logger = Logger::getLogger();
    }

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
    public function get($id)
    {
        $entry = $this->resolve($id);
        if (!($entry instanceof ReflectionClass)) {
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
    public function has($id): bool
    {
        $item = $this->resolve($id);

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
    public function set(string $key, $value): self
    {
        $this->services[$key] = $value;

        return $this;
    }

    /**
     * @param $id
     *
     * @return void|mixed|ReflectionClass
     */
    public function resolve($id)
    {
        if ($id === null) {
            return;
        }

        try {
            $name = $id;
            if (isset($this->services[$id])) {
                $name = $this->services[$id];
                if (is_callable($name)) {
                    return $name();
                }
                if (is_object($name)) {
                    return $name;
                }
            }
            return (new ReflectionClass($name));
        } catch (ReflectionException $e) {
            $this->logger->notice(
                'Container failed to resolve service.',
                [
                    'exception' => $e->getMessage(),
                    'potential cause' => 'Is this a request parameter?'
                ]
            );
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
        if ($constructor === null || $constructor->getNumberOfRequiredParameters() === 0) {
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

    /**
     * @param $class
     * @param $method
     *
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function getMethod($class, $method)
    {
        $reflector = new ReflectionClass($class);
        $params = $reflector->getMethod($method)->getParameters();

        $args = [];
        foreach ($params as $param) {
            $args[] = $this->getParameter($class, $method, $param);
        }

        $resolved = [];
        foreach ($args as $arg) {
            $resolved[] = $this->get($arg) ?: $arg;
        }

        return call_user_func_array([$class, $method], $resolved);
    }

    /**
     * @param $class
     * @param $method
     * @param $arg
     *
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function getParameter($class, $method, $arg)
    {
        $parameter = new ReflectionParameter([$class, $method], $arg->name);

        $type = $parameter->getType();

        if ($type !== null) {
            return $type->getName();
        }

        return $this->tryHttpParams($parameter->getName());
    }

    /**
     * @param $parameter
     *
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function tryHttpParams($parameter)
    {
        $request = $this->get(Request::class);

        foreach ($request->getParams() as $param => $value) {
            if ($parameter === $param) {
                return $value;
            }
        }
        foreach ($request->postParams() as $param => $value) {
            if ($parameter === $param) {
                return $value;
            }
        }
    }

    /**
     * @param Closure $closure
     *
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function injectClosure(Closure $closure)
    {
        $reflector = new ReflectionFunction($closure);
        $params = $reflector->getParameters();

        $resolved = [];
        foreach ($params as $param) {
            $resolved[] = $this->get($param->getType()->getName());
        }

        return call_user_func_array($closure, $resolved);
    }

    /**
     * @param Container|null $container
     *
     * @return Container
     */
    public static function setInstance(Container $container = null): Container
    {
        return static::$instance = $container;
    }
}
