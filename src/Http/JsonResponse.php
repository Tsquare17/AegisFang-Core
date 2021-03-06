<?php

namespace AegisFang\Http;

/**
 * Class JsonResponse
 * @package AegisFang\Http
 */
class JsonResponse extends Response
{
    /**
     * Prepare the response.
     *
     * @param array $content
     * @param array $headers
     * @param int   $statusCode
     *
     * @return JsonResponse
     */
    public function make($content = [], $headers = [], $statusCode = 200): JsonResponse
    {
        $this->setBody($content);

        $this->setHeader(['Content-Type' => 'application/json']);

        foreach ($headers as $header) {
            $this->setHeader($header);
        }

        $this->setStatusCode($statusCode);

        return $this;
    }

    /**
     * Send the response.
     * @throws \JsonException
     */
    public function dispatch(): string
    {
        foreach ($this->headers() as $key => $value) {
            header($key . ': ' . $value);
        }

        http_response_code($this->status());

        return json_encode($this->body(), JSON_THROW_ON_ERROR, 512);
    }
}
