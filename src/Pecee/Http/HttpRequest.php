<?php

namespace Pecee\Http;

class HttpRequest
{
    protected $url;
    protected $method;
    protected $headers;
    protected $options;
    protected $rawData;
    protected $data;
    protected $timeout;
    protected $returnHeader;
    protected $contentType;

    /**
     * HttpRequest constructor.
     * @param null $url
     * @throws \ErrorException
     */
    public function __construct($url = null)
    {
        if (\function_exists('curl_init') === false) {
            throw new \ErrorException('This service requires the CURL PHP extension.');
        }

        set_time_limit($this->timeout);

        $this->reset();
        $this->url = $url;
    }

    public function reset(): void
    {
        $this->url = null;
        $this->options = [];
        $this->headers = [];
        $this->data = [];
        $this->rawData = null;
        $this->method = null;
        $this->returnHeader = true;
        $this->contentType = null;
    }

    /**
     * Add header
     *
     * @param string $header
     *
     * @return static $this
     */
    public function addHeader($header): self
    {
        $this->headers[] = $header;

        return $this;
    }

    /**
     * Set headers array
     *
     * @param array $headers
     *
     * @return static $this
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Get all headers
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Add curl option
     *
     * @param string $option
     * @param string $value
     *
     * @return static $this
     */
    public function addOption($option, $value): self
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Set curl options
     *
     * @param array $options
     *
     * @return static $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Add post data
     *
     * @param string $key
     * @param string $value
     *
     * @return static $this
     */
    public function addPostData($key, $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Set postdata
     *
     * @param array $data
     *
     * @return static $this
     */
    public function setPostData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set raw postdata
     *
     * @param string $data
     *
     * @return static $this;
     */
    public function setRawPostData($data): self
    {
        $this->rawData = $data;

        return $this;
    }

    /**
     * Get post-data
     *
     * @return array
     */
    public function getPostData(): array
    {
        return $this->data;
    }

    /**
     * Get raw post-data
     *
     * @return string|null
     */
    public function getRawPostData()
    {
        return $this->rawData;
    }

    /**
     * Make post request
     *
     * @param bool $return
     *
     * @throws HttpException
     * @return HttpResponse $this
     */
    public function post($return = false): HttpResponse
    {
        $this->options[CURLOPT_POST] = true;

        return $this->execute($return);
    }

    /**
     * Make get request
     *
     * @param bool $return
     * @throws HttpException
     * @return HttpResponse
     */
    public function get($return = false): HttpResponse
    {
        return $this->execute($return);
    }

    /**
     * Set timeout
     *
     * @param int $timeout
     *
     * @return static $this
     */
    public function setTimeout($timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Set method
     *
     * @param string $method
     *
     * @return static $this
     */
    public function setMethod($method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param mixed $rawData
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return static $this
     */
    public function setData(array $data) : self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set content-type
     *
     * @param string $contentType
     *
     * @return static $this
     */
    public function setContentType($contentType): self
    {
        $this->contentType = \strtolower($contentType);

        return $this;
    }

    /**
     * Get request method
     * @return string|null
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get content-type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return static $this
     */
    public function setUrl($url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Defines if headers should be parsed when receiving response.
     *
     * @param bool $bool
     *
     * @return static $this
     */
    public function setReturnHeader($bool): self
    {
        $this->returnHeader = $bool;

        return $this;
    }

    /**
     * Set basic authentication
     *
     * @param $username
     * @param $password
     *
     * @return static $this
     */
    public function setBasicAuth($username, $password): self
    {
        $this->addHeader('Authorization: Basic ' . \base64_encode(\sprintf('%s:%s', $username, $password)));

        return $this;
    }

    /**
     * Execute
     * @param bool $return
     * @throws HttpException
     * @return HttpResponse
     */
    public function execute($return = true): HttpResponse
    {
        $handle = \curl_init();

        if ($this->url === null) {
            throw new HttpException('Missing required property: url');
        }

        if (\strtolower($this->method) === 'get' && \count($this->data) > 0) {
            $this->url .= ((strpos($this->url, '?') === false) ? '?' : '&');
        }

        \curl_setopt($handle, CURLOPT_URL, $this->url);

        $response = new HttpResponse();

        if ($this->contentType !== null) {
            $this->addHeader('Content-Type: ' . $this->contentType);
        }

        if ($this->returnHeader === true) {
            \curl_setopt($handle, CURLOPT_HEADER, true);
            \curl_setopt($handle, CURLOPT_HEADERFUNCTION, [&$response, 'parseHeader']);
        }

        if ($return === true) {
            \curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        }

        if ($this->timeout) {
            \curl_setopt($handle, CURLOPT_CONNECTTIMEOUT_MS, $this->timeout);
            \curl_setopt($handle, CURLOPT_TIMEOUT_MS, $this->timeout);
        }

        // Add request data
        if ($this->method && strtolower($this->method) !== 'get') {

            switch ($this->contentType) {
                default:
                    $data = $this->rawData;
                    break;
                case 'application/json':
                    $data = \json_encode($this->data);
                    break;
                case 'application/x-www-form-urlencoded':
                    $data = \http_build_query($this->data);
                    break;
            }

            foreach ((array)$this->headers as $key => $header) {
                if (\stripos($header, 'Content-Length:') !== false) {
                    unset($this->headers[$key]);
                }
            }

            $this->addHeader('Content-Length: ' . \strlen($data));

            \curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $this->method);
            \curl_setopt($handle, CURLOPT_POST, true);
            \curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        }

        // Add headers
        if (\count($this->headers) > 0) {
            \curl_setopt($handle, CURLOPT_HTTPHEADER, $this->headers);
        }

        // Add custom curl options
        if (\count($this->options) > 0) {
            foreach ((array)$this->options as $option => $value) {
                \curl_setopt($handle, $option, $value);
            }
        }

        $output = \curl_exec($handle);

        $response->setInfo(\curl_getinfo($handle))->setResponse($output, $this->returnHeader);

        \curl_close($handle);

        unset($output, $handle);

        return $response;
    }

}