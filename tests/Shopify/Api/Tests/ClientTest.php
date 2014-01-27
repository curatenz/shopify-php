<?php

namespace Shopify\Api\Tests;

class ClientTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {

        $this->httpClient = $this->getMock('Shopify\HttpClient');
        $this->api = new \Shopify\Api\Client($this->httpClient);

    }

    public function testGetRequest()
    {

        $shopName = 'mycoolshop';
        $clientSecret = 'ABC123XYZ';
        $permanentAccessToken = '0987654321';

        $this->api->setShopName($shopName);
        $this->api->setClientSecret($clientSecret);
        $this->api->setAccessToken($permanentAccessToken);

        $shopUri = "https://{$shopName}.myshopify.com";
        $this->assertEquals($shopUri, $this->api->getShopUri());

        $productUri = '/admin/product/632910392.json';
        $productResponse = $this->getProductResponse();
        $product = json_decode($productResponse);

        $this->httpClient->expects($this->once())
                         ->method('get')
                         ->with($shopUri . $productUri)
                         ->will($this->returnValue($productResponse));

        // retrieve a single product
        // @see http://docs.shopify.com/api/product#show
        $this->assertEquals($product, $this->api->get($productUri));

    }

    public function testRequestValidation()
    {

        $this->api->setClientSecret('hush');

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

    protected function getProductResponse()
    {
        return <<<JSON
{
  "product": {
    "body_html": "<p>It's the small iPod with one very big idea: Video. Now the world's most popular music player, available in 4GB and 8GB models, lets you enjoy TV shows, movies, video podcasts, and more. The larger, brighter display means amazing picture quality. In six eye-catching colors, iPod nano is stunning all around. And with models starting at just $149, little speaks volumes.</p>",
    "created_at": "2014-01-24T16:30:53-05:00",
    "handle": "ipod-nano",
    "id": 632910392,
    "product_type": "Cult Products",
    "published_at": "2007-12-31T19:00:00-05:00",
    "published_scope": "global",
    "template_suffix": null,
    "title": "IPod Nano - 8GB",
    "updated_at": "2014-01-24T16:30:53-05:00",
    "vendor": "Apple",
    "tags": "Emotive, Flash Memory, MP3, Music",
    "variants": [
      {
        "barcode": "1234_pink",
        "compare_at_price": null,
        "created_at": "2014-01-24T16:30:53-05:00",
        "fulfillment_service": "manual",
        "grams": 200,
        "id": 808950810,
        "inventory_management": "shopify",
        "inventory_policy": "continue",
        "option1": "Pink",
        "option2": null,
        "option3": null,
        "position": 1,
        "price": "199.00",
        "product_id": 632910392,
        "requires_shipping": true,
        "sku": "IPOD2008PINK",
        "taxable": true,
        "title": "Pink",
        "updated_at": "2014-01-24T16:30:53-05:00",
        "inventory_quantity": 10
      }
    ],
    "options": [
      {
        "id": 594680422,
        "name": "Title",
        "position": 1,
        "product_id": 632910392
      }
    ],
    "images": [
      {
        "created_at": "2014-01-24T16:30:53-05:00",
        "id": 850703190,
        "position": 1,
        "product_id": 632910392,
        "updated_at": "2014-01-24T16:30:53-05:00",
        "src": "http://cdn.shopify.com/s/files/1/0006/9093/3842/products/ipod-nano.png?v=1390599053"
      },
      {
        "created_at": "2014-01-24T16:30:53-05:00",
        "id": 562641783,
        "position": 2,
        "product_id": 632910392,
        "updated_at": "2014-01-24T16:30:53-05:00",
        "src": "http://cdn.shopify.com/s/files/1/0006/9093/3842/products/ipod-nano-2.png?v=1390599053"
      }
    ],
    "image": {
      "created_at": "2014-01-24T16:30:53-05:00",
      "id": 850703190,
      "position": 1,
      "product_id": 632910392,
      "updated_at": "2014-01-24T16:30:53-05:00",
      "src": "http://cdn.shopify.com/s/files/1/0006/9093/3842/products/ipod-nano.png?v=1390599053"
    }
  }
}
JSON;
    }
}
