<?php

namespace Pecee\Http\Rest;

use Pecee\Http\HttpResponse;

interface IRestResult
{

    public function api(?string $url = null, string $method = RestBase::METHOD_GET, array $data = array()): HttpResponse;

    public function execute(): HttpResponse;

    public function getService(): RestBase;

}