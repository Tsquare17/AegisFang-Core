<?php

namespace AegisFang\Http;

class Response
{
    use ResponseTrait;

    /**
     * Set the content of the response.
     *
     * @param $body
     *
     * @return Response
     */
    public function setBody($body): Response
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set a header on the response.
     *
     * @param array $header
     *
     * @return Response
     */
    public function setHeader(array $header): Response
    {
        $this->headers[key($header)] = current($header);

        return $this;
    }

    /**
     * Set the status code of the response.
     *
     * @param int $statusCode
     *
     * @return Response
     */
    public function setStatusCode(int $statusCode): Response
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}
