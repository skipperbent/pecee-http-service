<?php

namespace Pecee\Http\Rest;

use Pecee\Http\HttpResponse;

class RestCollection implements IRestResult
{
    /**
     * @var RestBase
     */
    protected RestBase $service;
    protected array $rows;

    public function __construct(RestBase $service)
    {
        $this->service = $service;
        $this->rows = array();
    }

    public function getRow(int $index)
    {
        return $this->rows[$index] ?? null;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function setRows(array $rows): void
    {
        $this->rows = $rows;
    }

    /**
     * Execute api call
     *
     * @param string|null $url
     * @param string $method
     * @param array|null $data
     *
     * @return HttpResponse
     * @throws RestException
     */
    public function api(?string $url = null, string $method = RestBase::METHOD_GET, array $data = array()): HttpResponse
    {
        return $this->service->api($url, $method, $data);
    }

    /**
     * Execute api call.
     *
     * Alias for $this->api();
     *
     * @return HttpResponse
     * @throws RestException
     */
    public function execute(): HttpResponse
    {
        return $this->api();
    }

    public function getService(): RestBase
    {
        return $this->service;
    }

    public function setService(RestBase $service): void
    {
        $this->service = $service;
    }
}