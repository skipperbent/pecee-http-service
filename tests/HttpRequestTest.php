<?php

class HttpRequestTest extends PHPUnit_Framework_TestCase
{

    public function testUpload()
    {

        $httpRequest = new \Pecee\Http\HttpRequest('http://www.google.dk');
        $response = $httpRequest->execute();

        $this->assertEquals(200, $response->getStatusCode());

    }

}
