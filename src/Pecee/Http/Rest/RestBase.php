<?php
namespace Pecee\Http\Rest;

use Pecee\Http\HttpException;
use Pecee\Http\HttpRequest;

class RestBase implements IRestBase
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    public static $METHODS = [
        self::METHOD_GET,
        self::METHOD_POST,
        self::METHOD_PUT,
        self::METHOD_DELETE
    ];

    protected $serviceUrl;

    /**
     * @var HttpRequest
     */
    protected $httpRequest;

    public function __construct()
    {
        $this->httpRequest = new HttpRequest();
    }

    /**
     * Get service url
     * @return string
     */
    public function getServiceUrl() : string
    {
        return $this->serviceUrl;
    }

    /**
     * Set service url
     *
     * @param string $serviceUrl
     *
     * @return static$this
     */
    public function setServiceUrl($serviceUrl) : self
    {
        $this->serviceUrl = $serviceUrl;

        return $this;
    }

    /**
     * @return HttpRequest
     */
    public function getHttpRequest() : HttpRequest
    {
        return $this->httpRequest;
    }

    /**
     * Execute api call.
     *
     * @param string|null $url
     * @param string $method
     * @param array|null $data
     *
     * @throws HttpException
     * @return \Pecee\Http\HttpResponse|mixed
     */
    public function api($url = null, $method = self::METHOD_GET, array $data = array())
    {
        if (\in_array($method, static::$METHODS, true) === false) {
            throw new RestException('Invalid request method');
        }

        if ($this->httpRequest->getRawPostData() !== null) {
            $data = [$this->httpRequest->getRawPostData()];

            if ($method !== static::METHOD_GET) {
                $this->httpRequest->setRawPostData($this->httpRequest->getRawPostData());
            }
        } else {
            $data = \array_merge($this->httpRequest->getPostData(), $data);

            if ($method !== static::METHOD_GET) {
                $this->httpRequest->setPostData($data);
            }
        }

        if ($method === static::METHOD_GET && \is_array($data) === true) {
            $separator = (\strpos($url, '?') !== false) ? '&' : '?';
            $url .= $separator . \http_build_query($data);
        }

        $apiUrl = trim($this->getServiceUrl(), '/') . (($url !== null) ? '/' . trim($url, '/') : '');

        $this->httpRequest->setUrl($apiUrl);
        $this->httpRequest->setMethod($method);

        return $this->httpRequest->execute();

    }

}