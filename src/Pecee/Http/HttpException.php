<?php

namespace Pecee\Http;

use Exception;

class HttpException extends Exception
{
    protected ?HttpResponse $httpResponse;

    public function __construct(string $message, int $code = 0, ?HttpResponse $httpResponse = null)
    {
        parent::__construct($message, $code);

        $this->httpResponse = $httpResponse;
    }

    public function getHttpResponse(): ?HttpResponse
    {
        return $this->httpResponse;
    }
}