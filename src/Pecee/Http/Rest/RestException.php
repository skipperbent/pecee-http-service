<?php
namespace Pecee\Http\Rest;

use Pecee\Http\HttpResponse;

class RestException extends \Exception {

	protected $httpResponse;

	public function __construct($message, $code = 0, HttpResponse $httpResponse = null) {
		parent::__construct($message , $code);

		$this->httpResponse = $httpResponse;
	}

	public function getHttpResponse() {
		return $this->httpResponse;
	}

}