<?php

use Pecee\Http\Rest\RestBase;
use Pecee\Http\Rest\RestCollection;
use Pecee\Http\Rest\RestItem;

class ServiceContent extends RestItem
{

    const TYPE = 'content';

    public function __construct()
    {
        parent::__construct(new ServiceBase());
        $this->getService()->setServiceEventListener($this);
    }

    /**
     * Returns result-collection specific for this service.
     *
     * @return ContentCollection
     */
    public function onCreateCollection(): \Pecee\Http\Rest\IRestCollection
    {
        return new ContentCollection($this->service);
    }

    /**
     * @return self
     */
    public function onCreateItem(): \Pecee\Http\Rest\IRestResult
    {
        $self = new self($this->service->getUsername(), $this->service->getSecret());
        $self->setService($this->service);

        return $self;
    }

    /**
     * Get queryable service result
     * @return ContentCollection
     */
    public function getCollection(): \Pecee\Http\Rest\IRestCollection
    {
        return $this->onCreateCollection();
    }

    /**
     * Save item
     *
     * @return static
     * @throws \Pecee\Http\HttpException
     */
    public function save(): RestItem
    {
        $this->row = $this->api(null, RestBase::METHOD_POST, $this->getPostData())->getRow();

        return $this;
    }

    public function setDevelopment($bool)
    {
        $this->service->setDevelopment($bool);

        return $this;
    }

    /**
     * @return ServiceBase
     */
    public function getService(): \Pecee\Http\Rest\IRestBase
    {
        return $this->service;
    }

}