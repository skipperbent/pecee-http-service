<?php

namespace Pecee\Http\Rest;

interface IRestResult {

	public function api($url = null, $method = RestBase::METHOD_GET, array $data = array());

	public function execute();

	public function getService();

}