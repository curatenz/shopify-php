<?php

namespace Shopify\Api\Tests;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testMessageAndCode()
    {

        $message = 'Not found';
        $code = 404;

        $info = array(
            'response_headers' => array(
                'http_status_message' => $message,
                'http_status_code' => $code,
            )
        );

        $exception = new \Shopify\Api\Exception($info);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());

    }

    public function testInfo()
    {

        $info = array(
            'key1' => "val1",
            'key2' => "val2",
        );

        $exception = new \Shopify\Api\Exception($info);

        $this->assertEquals($info, $exception->getInfo());

    }

}

