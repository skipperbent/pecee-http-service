<?php
namespace Pecee\Http\Rest;

class RestCollection implements IRestResult {

    /**
     * @var RestBase
     */
    protected $service;
    protected $rows;

    public function __construct(RestBase $service) {
        $this->service = $service;
        $this->rows = array();
    }

    public function getRow($index) {
        return (isset($this->rows[$index])) ? $this->rows[$index] : null;
    }

    public function getRows() {
        return $this->rows;
    }

    public function setRows(array $rows) {
        $this->rows = $rows;
    }

    /**
     * Execute api call
     *
     * @param null $url
     * @param string $method
     * @param array|null $data
     *
     * @throws \Pecee\Http\Rest\RestException
     * @return static
     */
    public function api($url = null, $method = RestBase::METHOD_GET, array $data = array()) {
        return $this->service->api($url, $method, $data);
    }

    /**
     * Execute api call.
     *
     * Alias for $this->api();
     *
     * @return static
     */
    public function execute() {
        return $this->api();
    }

    public function getService() {
        return $this->service;
    }

    public function setService(RestBase $service) {
        $this->service = $service;
    }
}