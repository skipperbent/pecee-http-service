<?php

namespace Pecee\Http;

class HttpResponse
{
    protected $response;
    protected $info = [];
    protected $headers = [];

    public function parseHeader($handle, $header)
    {
        $details = explode(':', $header, 2);

        if (count($details) === 2) {
            $key = trim($details[0]);
            $value = trim($details[1]);

            if (isset($this->headers[$key]) === true) {
                $this->headers[$key][] = $value;
            } else {
                $this->headers[$key] = [$value];
            }
        }

        return strlen($header);
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getUrl()
    {
        return isset($this->info['url']) ? $this->info['url'] : null;
    }

    public function getContentType()
    {
        return isset($this->info['content_type']) ? $this->info['content_type'] : null;
    }

    public function getRequestSize()
    {
        return isset($this->info['request_size']) ? $this->info['request_size'] : null;
    }

    public function getHeaderSize()
    {
        return isset($this->info['header_size']) ? $this->info['header_size'] : null;
    }

    public function getStatusCode()
    {
        return isset($this->info['http_code']) ? $this->info['http_code'] : null;
    }

    public function getTotalTime()
    {
        return isset($this->info['total_time']) ? $this->info['total_time'] : null;
    }

    /**
     * Set response-info
     *
     * @param array $info
     * @return static $this
     */
    public function setInfo(array $info)
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
                return count($value) === 1 ? $value[0] : $value;
            }
        }

        return $default;
    }

    /**
     * Get headers
     * @return array
     */
    public function getHeaders()
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
    public function setResponse($response, $removeHeaders = false)
    {
        $this->response = $response;

        if ($removeHeaders === true) {
            $this->response = substr($response, $this->getHeaderSize());
        }

        return $this;
    }

}