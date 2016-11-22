<?php
namespace Pecee\Http\Rest;

use Pecee\Http\HttpRequest;

class RestBase {
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    public static $METHODS = array(self::METHOD_GET, self::METHOD_POST, self::METHOD_PUT, self::METHOD_DELETE);

    protected $serviceUrl;

    /**
     * @var HttpRequest
     */
    protected $httpRequest;

    public function __construct() {
        $this->httpRequest = new HttpRequest();
    }

    public function getServiceUrl() {
        return $this->serviceUrl;
    }

    public function setServiceUrl($serviceUrl) {
        $this->serviceUrl = $serviceUrl;
    }

    /**
     * @return HttpRequest
     */
    public function getHttpRequest() {
        return $this->httpRequest;
    }

    /**
     * Execute api call.
     *
     * @param string|null $url
     * @param string $method
     * @param array|null $data
     * @throws \Pecee\Http\Rest\RestException
     * @return \Pecee\Http\HttpResponse|mixed
     */
    public function api($url = null, $method = self::METHOD_GET, array $data = array()) {
        if(!in_array($method, self::$METHODS)) {
            throw new RestException('Invalid request method');
        }

        if($this->httpRequest->getRawPostData() !== null) {
            $data = $this->httpRequest->getRawPostData();

            if($method !== self::METHOD_GET) {
                $this->httpRequest->setRawPostData($data);
            }
        } else {
            $data = array_merge($this->httpRequest->getPostData(), $data);

            if($method !== self::METHOD_GET) {
                $this->httpRequest->setPostData($data);
            }
        }

        if($method === self::METHOD_GET && is_array($data)) {
            $separator = (strpos($url, '?') !== false) ? '&' : '?';
            $url .= $separator . http_build_query($data);
        }

        $apiUrl = trim($this->getServiceUrl(), '/') . (($url !== null) ? '/' . trim($url, '/') : '');

        $this->httpRequest->setUrl($apiUrl);
        $this->httpRequest->setMethod($method);

        $response = $this->httpRequest->execute(true);

        return $response;
    }

}