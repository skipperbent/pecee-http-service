<?php
namespace Pecee\Http;

class HttpResponse
{
    protected $response;
    protected $info = [];
    protected $headers = [];

    public function parseHeader($handle, $header) : int
    {
        $details = explode(':', $header, 2);

        if (\count($details) === 2) {
            $key   = trim($details[0]);
            $value = trim($details[1]);

            $this->headers[$key] = $value;
        }

        return \strlen($header);
    }

    public function getInfo() : array
    {
        return $this->info;
    }

    public function getResponse() : HttpResponse
    {
        return $this->response;
    }

    public function getUrl() : string
    {
        return $this->info['url'] ?? null;
    }

    public function getContentType()
    {
        return $this->info['content_type'] ?? null;
    }

    public function getRequestSize()
    {
        return $this->info['request_size'] ?? null;
    }

    public function getHeaderSize()
    {
        return $this->info['header_size'] ?? null;
    }

    public function getStatusCode()
    {
        return $this->info['http_code'] ?? null;
    }

    public function getTotalTime()
    {
        return $this->info['total_time'] ?? null;
    }

    /**
     * Set response-info
     *
     * @param array $info
     * @return static $this
     */
    public function setInfo(array $info) : self
    {
        $this->info = $info;
        return $this;
    }

    /**
     * Get header by key
     *
     * @param $key
     * @param string|null $default
     *
     * @return string|null
     */
    public function getHeader($key, $default = null)
    {
        foreach ($this->headers as $k => $value) {
            if (strtolower($key) === strtolower($k)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Get headers
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Set response
     *
     * @param string $response
     * @param bool $removeHeaders
     * @return static $this
     */
    public function setResponse($response, $removeHeaders = false) : self
    {
        $this->response = $response;

        if ($removeHeaders === true) {
            $this->response = substr($response, $this->getHeaderSize());
        }

        return $this;
    }

}