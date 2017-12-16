<?php

use Pecee\Http\HttpException;
use Pecee\Http\HttpResponse;
use Pecee\Http\Rest\IRestEventListener;
use Pecee\Http\Rest\RestBase;

class ServiceBase extends RestBase
{
    protected $serviceUrl = 'https://jsonplaceholder.typicode.com/users';
    protected $serviceEventListener;

    public function __construct()
    {
        parent::__construct();

        $this->httpRequest->setTimeout(20000);
    }

    /**
     * Parses the API-response and returns either a collection object or single item depending on the results.
     *
     * @param HttpResponse $httpResponse
     * @return mixed
     */
    protected function onResponseReceived(HttpResponse $httpResponse)
    {

    }

    /**
     * @param null $url
     * @param string $method
     * @param array $data
     * @return mixed
     * @throws HttpException
     */
    public function api($url = null, $method = self::METHOD_GET, array $data = []): mixed
    {

        $data = array_merge($this->httpRequest->getPostData(), $data);

        // Execute the API-call
        return $this->onResponseReceived(parent::api($url, $method, $data));
    }

    /**
     * @return IRestEventListener
     */
    public function getServiceEventListener(): IRestEventListener
    {
        return $this->serviceEventListener;
    }

    /**
     * @param IRestEventListener $serviceEventListener
     */
    public function setServiceEventListener(IRestEventListener $serviceEventListener)
    {
        $this->serviceEventListener = $serviceEventListener;
    }

}