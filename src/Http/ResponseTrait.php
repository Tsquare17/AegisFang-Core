<?php

namespace AegisFang\Http;

trait ResponseTrait
{
    /*
     * @var $body
     */
    protected $body;

    /*
     * @var $headers
     */
    protected array $headers;

    /*
     * @var $statusCode
     */
    protected int $statusCode;

    /**
     * Get the content of the response.
     *
     * @return mixed
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * Get the headers of the response.
     *
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Get the status code of the response.
     *
     * @return int
     */
    public function status(): int
    {
        return $this->statusCode;
    }
}
