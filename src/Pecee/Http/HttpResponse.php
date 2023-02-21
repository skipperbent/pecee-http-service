<?php

namespace Pecee\Http;

class HttpResponse
{
    protected string $response = '';
    protected array $info = [];
    protected array $headers = [];

    public function parseHeader($handle, $header): int
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

    public function getInfo(): array
    {
        return $this->info;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getUrl(): ?string
    {
        return $this->info['url'] ?? null;
    }

    public function getContentType(): ?string
    {
        return $this->info['content_type'] ?? null;
    }

    public function getRequestSize(): ?int
    {
        return $this->info['request_size'] ?? null;
    }

    public function getHeaderSize(): ?int
    {
        return $this->info['header_size'] ?? null;
    }

    public function getStatusCode(): ?int
    {
        return $this->info['http_code'] ?? null;
    }

    public function getTotalTime(): ?int
    {
        return $this->info['total_time'] ?? null;
    }

    /**
     * Set response-info
     *
     * @param array $info
     * @return static $this
     */
    public function setInfo(array $info): self
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get header by key
     *
     * @param string $key
     * @param string|null $default
     *
     * @return string|null
     */
    public function getHeader(string $key, ?string $default = null): ?string
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
    public function getHeaders(): array
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
    public function setResponse(string $response, bool $removeHeaders = false): self
    {
        $this->response = $response;

        if ($removeHeaders === true) {
            $this->response = substr($response, $this->getHeaderSize());
        }

        return $this;
    }

    public function getResponseArray(): array
    {
        return json_decode($this->getResponse(), true);
    }

}