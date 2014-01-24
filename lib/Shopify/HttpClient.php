<?php

namespace Shopify;

interface HttpClient
{

    const POST = 'post';
    const GET = 'get';

    /**
     * make a get request to the given uri
     * @param string $uri
     * @param string $resource
     * @param array $params
     * @return mixed
     */
    public function get($uri, $resource, array $params = array());

    /**
     * make a post request to the given uri
     * @param string $uri
     * @param string $resource
     * @param array $params
     * @return mixed
     */
    public function post($uri, $resource, array $params = array());

}
