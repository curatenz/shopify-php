<?php

namespace Shopify\HttpClient;

use Shopify\HttpClient;

abstract class HttpClientAdapter implements HttpClient
{

    /** @var string */
    protected $accessToken;

    public function setShopifyAccessToken($token)
    {
        $this->accessToken = $token;
    }

    abstract protected function applyShopifyAccessTokenToRequest();

}
