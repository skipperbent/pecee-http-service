<?php
namespace Pecee\Http\Rest;

use Pecee\Http\HttpException;

class RestItem implements IRestResult, IRestEventListener
{
    protected $primaryKey = 'id';
    protected $row = array();
    protected $service;

    public function __construct(RestBase $service)
    {
        $this->service = $service;
    }

    public function setRow(array $row)
    {
        $this->row = $row;
    }

    public function __set($name, $value = null)
    {
        $this->row->$name = $value;
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->row);
    }

    public function __get($name)
    {
        return isset($this->row->$name) ? $this->row->{$name} : null;
    }

    public function getRow()
    {
        return $this->row;
    }

    /**
     * Returns result-collection specific for this service.
     *
     * @return IRestCollection
     */
    public function onCreateCollection() : IRestCollection
    {
        return new RestCollection($this->service);
    }

    /**
     * @return IRestResult
     */
    public function onCreateItem() : IRestResult
    {
        return new static($this->service);
    }

    /**
     * Get single item by id
     *
     * @param string $id
     *
     * @throws HttpException
     * @return static
     */
    public function getById($id)
    {

        $this->row = $this->api($id)->getRow();

        return $this;
    }

    /**
     * Delete item
     *
     * @throws HttpException
     * @return static
     */
    public function delete() : self
    {
        if ($this->{$this->primaryKey} === null) {
            throw new RestException(sprintf('Failed to delete. Missing required argument "%s"', $this->primaryKey));
        }
        $this->row = $this->api($this->id, RestBase::METHOD_DELETE)->getRow();

        return $this;
    }

    /**
     * Update item
     *
     * @throws HttpException
     * @return static
     */
    public function update() : self
    {
        if ($this->{$this->primaryKey} === null) {
            throw new RestException(sprintf('Failed to update. Missing required argument "%s"', $this->primaryKey));
        }

        $this->row = $this->api($this->id, RestBase::METHOD_PUT, (array)$this->row)->getRow();

        return $this;
    }

    /**
     * Save item
     *
     * @throws HttpException
     * @return static
     */
    public function save() : self
    {
        $this->row = $this->api(null, RestBase::METHOD_POST, (array)$this->row)->getRow();

        return $this;
    }

    /**
     * @return IRestBase
     */
    public function getService() : IRestBase
    {
        return $this->service;
    }

    /**
     * @param IRestBase $service
     */
    public function setService(IRestBase $service)
    {
        $this->service = $service;
    }

    /**
     * @param null $url
     * @param string $method
     * @param array $data
     * @return mixed|\Pecee\Http\HttpResponse
     * @throws HttpException
     */
    public function api($url = null, $method = RestBase::METHOD_GET, array $data = array())
    {
        return $this->service->api($url, $method, $data);
    }

    /**
     * @return mixed|\Pecee\Http\HttpResponse
     * @throws HttpException
     */
    public function execute()
    {
        return $this->api();
    }

}