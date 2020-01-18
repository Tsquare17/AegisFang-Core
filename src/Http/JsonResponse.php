<?php

namespace AegisFang\Http;

class JsonResponse extends Response
{
    /**
     * Make
     *
     * @param array $content
     * @param array $headers
     * @param int   $statusCode
     *
     * @return Response
     */
    public function make($content = [], $headers = [], $statusCode = 200): Response
    {
        $this->setBody($content);

        $this->setHeader(['Content-Type' => 'application/json']);

        foreach ($headers as $header) {
            $this->setHeader($header);
        }

        $this->setStatusCode($statusCode);

        return $this;
    }

    public function send(): void
    {
        foreach ($this->headers() as $key => $value) {
            header($key . ': ' . $value);
            echo $key . ': ' . $value;
        }

        http_response_code($this->status());

        echo json_encode($this->body(), JSON_THROW_ON_ERROR, 512);
    }
}
