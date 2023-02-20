<?php

namespace Pecee\Http\Rest;

use Pecee\Http\HttpResponse;

class RestItem implements IRestResult, IRestEventListener
{
    protected string $primaryKey = 'id';
    protected array $row = array();
    protected RestBase $service;

    public function __construct(RestBase $service)
    {
        $this->service = $service;
    }

    public function setRow(array $row): void
    {
        $this->row = $row;
    }

    public function __set(string $name, $value = null): void
    {
        $this->row->$name = $value;
    }

    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->row);
    }

    public function __get(string $name)
    {
        return isset($this->row->$name) ? $this->row->{$name} : null;
    }

    public function getRow(): array
    {
        return $this->row;
    }

    /**
     * Returns result-collection specific for this service.
     *
     * @return \Pecee\Http\Rest\RestCollection
     */
    public function onCreateCollection(): RestCollection
    {
        return new RestCollection($this->service);
    }

    /**
     * @return self
     */
    public function onCreateItem(): self
    {
        return new static($this->service);
    }

    /**
     * Get single item by id
     *
     * @param string $id
     *
     * @return static
     * @throws \Pecee\Http\Rest\RestException
     */
    public function getById(string $id): self
    {
        if ($this->{$this->primaryKey} === null) {
            throw new RestException(sprintf('Missing required argument "%s"', $this->primaryKey));
        }
        $this->row = $this->api($id)->getResponseArray();

        return $this;
    }

    /**
     * Delete item
     *
     * @return static
     * @throws \Pecee\Http\Rest\RestException
     */
    public function delete(): self
    {
        if ($this->{$this->primaryKey} === null) {
            throw new RestException(sprintf('Failed to delete. Missing required argument "%s"', $this->primaryKey));
        }
        $this->row = $this->api($this->id, RestBase::METHOD_DELETE)->getResponseArray();

        return $this;
    }

    /**
     * Update item
     *
     * @return static
     * @throws \Pecee\Http\Rest\RestException
     */
    public function update(): self
    {
        if ($this->{$this->primaryKey} === null) {
            throw new RestException(sprintf('Failed to update. Missing required argument "%s"', $this->primaryKey));
        }

        $this->row = $this->api($this->id, RestBase::METHOD_PUT, $this->row)->getResponseArray();

        return $this;
    }

    /**
     * @return static $this
     * @throws RestException
     */
    public function save(): self
    {
        $this->row = $this->api(null, RestBase::METHOD_POST, $this->row)->getResponseArray();

        return $this;
    }

    /**
     * @return RestBase
     */
    public function getService(): RestBase
    {
        return $this->service;
    }

    /**
     * @param RestBase $service
     */
    public function setService(RestBase $service): void
    {
        $this->service = $service;
    }

    /**
     * @param string|null $url
     * @param string $method
     * @param array $data
     * @return HttpResponse
     * @throws RestException
     */
    public function api(?string $url = null, string $method = RestBase::METHOD_GET, array $data = array()): HttpResponse
    {
        return $this->service->api($url, $method, $data);
    }

    /**
     * @return HttpResponse
     * @throws RestException
     */
    public function execute(): HttpResponse
    {
        return $this->api();
    }

}