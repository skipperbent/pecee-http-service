<?php
namespace Pecee\Http\Rest;

class RestItem implements IRestResult, IRestEventListener {

    protected $row;
    protected $service;

    public function __construct(RestBase $service) {
        $this->row = new \stdClass();
        $this->service = $service;
    }

    public function setRow(\stdClass $row = null) {
        $this->row = $row;
    }

    public function __set($name, $value = null) {
        $this->row->$name = $value;
    }

    public function __get($name) {
        return (isset($this->row->$name)) ? $this->row->$name : null;
    }

    public function getRow() {
        return $this->row;
    }

    /**
     * Returns result-collection specific for this service.
     *
     * @return \Pecee\Http\Rest\RestCollection
     */
    public function onCreateCollection() {
        return new RestCollection($this->service);
    }
    /**
     * @return self
     */
    public function onCreateItem() {
        return new static($this->service);
    }

    /**
     * Get single item by id
     *
     * @param string $id
     * @throws \Pecee\Http\Rest\RestException
     * @return static
     */
    public function getById($id) {
        if($this->id === null) {
            throw new RestException('Missing required argument "id".');
        }
        $this->row = $this->api($id)->getRow();
        return $this;
    }

    /**
     * Delete item
     *
     * @throws \Pecee\Http\Rest\RestException
     * @return static
     */
    public function delete() {
        if($this->id === null) {
            throw new RestException('Failed to delete. Missing required argument "id".');
        }
        $this->row = $this->api($this->id, RestBase::METHOD_DELETE)->getRow();
    }

    /**
     * Update item
     *
     * @throws \Pecee\Http\Rest\RestException
     * @return static
     */
    public function update() {
        if($this->id === null) {
            throw new RestException('Failed to update. Missing required argument "id".');
        }

        $this->row = $this->api($this->id, RestBase::METHOD_PUT, (array)$this->row)->getRow();
        return $this;
    }

    /**
     * Save item
     *
     * @throws \Pecee\Http\Rest\RestException
     * @return static
     */
    public function save() {
        $this->row = $this->api(null, RestBase::METHOD_POST, (array)$this->row)->getRow();
        return $this;
    }

    /**
     * @return RestBase
     */
    public function getService() {
        return $this->service;
    }

    /**
     * @param RestBase $service
     */
    public function setService(RestBase $service) {
        $this->service = $service;
    }

    public function api($url = null, $method = RestBase::METHOD_GET, array $data = array()) {
        return $this->service->api($url, $method, $data);
    }

    public function execute() {
        return $this->api();
    }

}