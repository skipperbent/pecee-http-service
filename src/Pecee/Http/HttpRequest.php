<?php

namespace Pecee\Http;

class HttpRequest {

	protected $url;
	protected $method;
	protected $headers;
	protected $options;
	protected $data;
	protected $timeout;
	protected $postJson;

	public function __construct($url = null) {

		if (!function_exists('curl_init')) {
			throw new \Exception('This service requires the CURL PHP extension.');
		}

		$this->reset();
		$this->url = $url;
	}

	public function reset() {
		$this->url = null;
		$this->options = array();
		$this->headers = array();
		$this->data = array();
		$this->postJson = false;
	}

	public function addHeader($header) {
		$this->headers[] = $header;
	}

	public function setHeaders(array $headers) {
		$this->headers = $headers;
	}

	public function addOption($option, $value) {
		$this->options[$option] = $value;
	}

	public function setOptions(array $options) {
		$this->options = $options;
	}

	public function addPostData($key, $value) {
		$this->data[$key] = $value;
	}

	public function setPostData(array $data) {
		$this->data = $data;
	}

	public function getPostData() {
		return $this->data;
	}

	public function post($return = false) {
		$this->options[CURLOPT_POST] = true;
		$this->execute($return);
	}

	public function get($return = false) {
		// Alias for execute
		$this->execute($return);
	}

	public function setTimeout($timeout) {
		$this->timeout = $timeout;
	}

	public function setMethod($method) {
		$this->method = $method;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @return bool
	 */
	public function getPostJson() {
		return $this->postJson;
	}

	/**
	 * @param bool $postJson
	 */
	public function setPostJson($postJson) {
		$this->postJson = $postJson;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * Set basic authentication
	 *
	 * @param $username
	 * @param $password
	 */
	public function setBasicAuth($username, $password) {
		$this->addHeader('Authorization: Basic ' . base64_encode(sprintf('%s:%s', $username, $password)));
	}

	public function execute($return) {
		$handle = curl_init();

		if($this->url === null) {
			throw new \InvalidArgumentException('Missing required property: url');
		}

		curl_setopt($handle, CURLOPT_URL, $this->url);

		if($return) {
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		}

		if($this->timeout) {
			curl_setopt($handle, CURLOPT_TIMEOUT_MS, $this->timeout);
		}

		// Add request data
		if($this->method && strtolower($this->method) !== 'get' && is_array($this->data)) {
			$data = ($this->postJson) ? json_encode($this->data) : http_build_query($this->data);
			$this->addHeader('Content-length: ' . strlen($data));

			curl_setopt($handle, CURLOPT_POST, true);
			curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		}

		// Add headers
		if(count($this->headers)) {
			curl_setopt($handle, CURLOPT_HTTPHEADER, $this->headers);
		}

		// Add custom curl options
		if(count($this->options)) {
			foreach($this->options as $option => $value) {
				curl_setopt($handle, $option, $value);
			}
		}

		return new HttpResponse($handle);
	}

}