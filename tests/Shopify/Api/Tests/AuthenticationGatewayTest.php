<?php

namespace Shopify\Api\Tests;

class AuthenticationGatewayTest extends \PHPUnit_Framework_TestCase
{

    public function testInitiatingLogin()
    {

        $shopName = 'shop-name';
        $clientId = 'XXX1234567890';
        $permissions = array('write_products', 'read_orders');
        $redirectUri = 'http://shopify.com/app';

        $authorizeUrl = "https://{$shopName}.myshopify.com"
            . "/admin/oauth/authorize"
            . "?" . http_build_query(array(
                'client_id' => $clientId,
                'scope' => join(',', $permissions),
                'redirect_uri' => $redirectUri
            ));

        $httpClient = $this->getMock('Shopify\HttpClient');
        $redirector = $this->getMock('Shopify\Redirector');

        $redirector->expects($this->once())
                   ->method('redirect')
                   ->with($authorizeUrl)
                   ->will($this->returnValue($redirectUri));

        $authenticate = new \Shopify\Api\AuthenticationGateway(
            $httpClient, $redirector
        );

        $authenticate->forShopName($shopName)
                     ->usingClientId($clientId)
                     ->withScope($permissions)
                     ->andReturningTo($redirectUri)
                     ->initiateLogin();

        $this->assertEquals(
            $authorizeUrl, $authenticate->getAuthenticationUri()
        );

    }

}
