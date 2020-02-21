<?php

namespace AegisFang\Http;

/**
 * Class Request
 * @package AegisFang\Router
 */
class Request
{
    protected array $postParams = [];
    protected array $getParams = [];
    protected string $url;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->collectGetParams();
        $this->collectPostParams();
    }

    /**
     * Get the uri, trimmed and stripped of url parameters.
     *
     * @return string
     */
    public static function uri(): string
    {
        return trim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
    }

    /**
     * Get the HTTP request method.
     *
     * @return string
     */
    public static function method(): string
    {
        return $_POST['REQUEST_METHOD_OVERRIDE'] ?? $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Collect $_POST parameters.
     */
    protected function collectPostParams(): void
    {
        foreach ($_POST as $param => $value) {
            $this->postParams[$param] = $value;
        }
    }

    /**
     * Collect $_GET parameters.
     */
    protected function collectGetParams(): void
    {
        foreach ($_GET as $param => $value) {
            $this->getParams[$param] = $value;
        }
    }

    /**
     * Get the POST parameters.
     *
     * @return array
     */
    public function postParams(): array
    {
        return $this->postParams;
    }

    /**
     * Get the GET parameters.
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->getParams;
    }

    /**
     * Access parameters like class properties.
     *
     * @param $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->getParams()[$name] ?? $this->postParams()[$name] ?? null;
    }
}
