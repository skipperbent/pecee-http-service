<?php
namespace Pecee\Http;

class HttpRequest
{
    protected ?string $url;
    protected ?string $method;
    protected array $headers;
    protected array $options;
    protected ?string $rawData;
    protected array $data;
    protected int $timeout;
    protected bool $returnHeader;
    protected ?string $contentType;

    /**
     * HttpRequest constructor.
     * @param string|null $url
     * @throws \ErrorException
     */
    public function __construct(?string $url = null)
    {
        if (function_exists('curl_init') === false) {
            throw new \ErrorException('This service requires the CURL PHP extension.');
        }

        $this->reset();
        $this->url = $url;
    }

    public function reset()
    {
        $this->url          = null;
        $this->options      = [];
        $this->headers      = [];
        $this->data         = [];
        $this->rawData      = null;
        $this->method       = null;
        $this->returnHeader = true;
        $this->contentType  = null;
    }

    /**
     * Add header
     *
     * @param string $header
     *
     * @return static $this
     */
    public function addHeader(string $header): self
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
    public function addOption(string $option, string $value): self
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
    public function addPostData(string $key, string $value): self
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
    public function setRawPostData(string $data): self
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
     * @return string
     */
    public function getRawPostData(): string
    {
        return $this->rawData;
    }

    /**
     * Make post request
     *
     * @param bool $return
     *
     * @return HttpResponse $this
     */
    public function post(bool $return = false): HttpResponse
    {
        $this->options[CURLOPT_POST] = true;

        return $this->execute($return);
    }

    /**
     * Make get request
     *
     * @param bool $return
     *
     * @return HttpResponse
     */
    public function get(bool $return = false): HttpResponse
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
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Set method
     *
     * @param string|null $method
     *
     * @return static $this
     */
    public function setMethod(?string $method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Set content-type
     *
     * @param string|null $contentType
     *
     * @return static $this
     */
    public function setContentType(?string $contentType): self
    {
        $this->contentType = strtolower($contentType);

        return $this;
    }

    /**
     * Get content-type
     *
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * Get url
     *
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     *
     * @return static $this
     */
    public function setUrl(?string $url): self
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
    public function setReturnHeader(bool $bool): self
    {
        $this->returnHeader = $bool;

        return $this;
    }

    /**
     * Set basic authentication
     *
     * @param string $username
     * @param string $password
     *
     * @return static $this
     */
    public function setBasicAuth(string $username, string $password): self
    {
        $this->addHeader('Authorization: Basic ' . base64_encode(sprintf('%s:%s', $username, $password)));

        return $this;
    }

    public function execute(bool $return = true): HttpResponse
    {
        $handle = curl_init();

        if ($this->url === null) {
            throw new \InvalidArgumentException('Missing required property: url');
        }

        if (strtolower($this->method) === 'get' && count($this->getPostData()) > 0) {
            $this->url .= ((strpos($this->url, '?') === false) ? '?' : '&');
        }

        curl_setopt($handle, CURLOPT_URL, $this->url);

        $response = new HttpResponse();

        if ($this->contentType !== null) {
            $this->addHeader('Content-Type: ' . $this->contentType);
        }

        if ($this->returnHeader === true) {
            curl_setopt($handle, CURLOPT_HEADER, true);
            curl_setopt($handle, CURLOPT_HEADERFUNCTION, [&$response, 'parseHeader']);
        }

        if ($return === true) {
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        }

        if ($this->timeout > 0) {
            // Disable PHP timeout
            set_time_limit($this->timeout);

            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT_MS, $this->timeout);
            curl_setopt($handle, CURLOPT_TIMEOUT_MS, $this->timeout);
        }

        // Add request data
        if ($this->method && strtolower($this->method) !== 'get') {

            switch ($this->contentType) {
                default:
                    $data = $this->rawData;
                    break;
                case 'application/json':
                    $data = json_encode($this->data);
                    break;
                case 'application/x-www-form-urlencoded':
                    $data = http_build_query($this->data);
                    break;
            }

            foreach ($this->headers as $key => $header) {
                if (stripos($header, 'content-length:') !== false) {
                    unset($this->headers[$key]);
                }
            }

            $this->addHeader('Content-length: ' . strlen($data));

            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $this->method);
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
        }

        // Add headers
        if (count($this->headers) > 0) {
            curl_setopt($handle, CURLOPT_HTTPHEADER, $this->headers);
        }

        // Add custom curl options
        if (count($this->options) > 0) {
            foreach ($this->options as $option => $value) {
                curl_setopt($handle, $option, $value);
            }
        }

        $output = curl_exec($handle);

        $response->setInfo(curl_getinfo($handle))->setResponse($output, $this->returnHeader);

        curl_close($handle);

        unset($output, $handle);

        return $response;
    }

}