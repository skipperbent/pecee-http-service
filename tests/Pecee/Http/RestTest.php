<?php
namespace Pecee\Http;

class RestTest extends \PHPUnit_Framework_TestCase
{

    public function testHello()
    {

        $users = new \ServiceContent();
        $user = $users->getById(1);

        die(var_dump($user));

    }

}
