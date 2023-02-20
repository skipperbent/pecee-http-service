<?php

namespace Pecee\Http\Rest;

interface IRestResult
{

    public function api(?string $url = null, string $method = RestBase::METHOD_GET, array $data = array());

    public function execute();

    public function getService(): RestBase;

}