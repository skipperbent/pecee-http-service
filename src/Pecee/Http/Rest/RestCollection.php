<?php

namespace Pecee\Http\Rest;

use Pecee\Http\HttpException;

class RestCollection implements IRestResult, IRestCollection
{
    /**
     * @var RestBase
     */
    protected $service;
    protected $rows;

    public function __construct(RestBase $service)
    {
        $this->service = $service;
        $this->rows = [];
    }

    /**
     * @param int $index
     * @return mixed
     */
    public function getRow($index): mixed
    {
        return $this->rows[$index] ?? null;
    }

    /**
     * @return array
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @param array $rows
     * @return static
     */
    public function setRows(array $rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * Execute api call
     *
     * @param null $url
     * @param string $method
     * @param array|null $data
     *
     * @throws HttpException
     * @return static
     */
    public function api($url = null, $method = RestBase::METHOD_GET, array $data = []): self
    {
        return $this->service->api($url, $method, $data);
    }

    /**
     * Execute api call.
     *
     * Alias for $this->api();
     *
     * @return static
     * @throws HttpException
     */
    public function execute(): self
    {
        return $this->api();
    }

    /**
     * @return IRestBase
     */
    public function getService(): IRestBase
    {
        return $this->service;
    }

    /**
     * @param IRestBase $service
     * @return static
     */
    public function setService(IRestBase $service): self
    {
        $this->service = $service;

        return $this;
    }
}