<?php
namespace Pecee\Http;

class HttpException extends \Exception
{
	protected $httpResponse;

	public function __construct($message, $code = 0, HttpResponse $httpResponse = null)
	{
		parent::__construct($message, $code);

		$this->httpResponse = $httpResponse;
	}

	public function getHttpResponse()
	{
		return $this->httpResponse;
	}
}