<?php

namespace Shopify\Api\Tests;

class ClientTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {

        $http = $this->getMock('Shopify\HttpClient');
        $this->api = new \Shopify\Api\Client($http);

    }

    public function testRequestValidation()
    {

        $this->api->setSharedSecret('hush');

        $signature = "31b9fcfbd98a3650b8523bcc92f8c5d2";

        // Assume we have the query parameters in a hash
        $params = array(
            'shop' => "some-shop.myshopify.com",
            'code' => "a94a110d86d2452eb3e2af4cfb8a3828",
            'timestamp' => "1337178173", // 2012-05-16 14:22:53
        );

        $this->assertEquals($signature, $this->api->generateSignature($params));

        $paramsWithSignature = $params;
        $paramsWithSignature['signature'] = $signature;

        $this->assertTrue($this->api->validateSignature($paramsWithSignature));

        // request is older than 1 day, expect false
        $this->assertFalse($this->api->isValidRequest($paramsWithSignature));

    }

    public function testCallLimits()
    {

        $callsMade = 10;
        $callLimit = 100;
        $callsRemaining = $callLimit - $callsMade;

        $headers = array(
            \Shopify\Api\Client::SHOP_API_CALL_LIMIT => '10/100'
        );

        $this->assertEquals(
            $callsMade, $this->api->getNumberOfCallsMade($headers)
        );

        $this->assertEquals(
            $callLimit, $this->api->getCallLimit($headers)
        );

        $this->assertEquals(
            $callsRemaining, $this->api->getNumberOfCallsRemaining($headers)
        );

    }

}
