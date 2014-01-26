<?php

namespace Shopify\HttpClient;

class BuzzAdapter implements \Shopify\HttpClient
{

    /** @var \Buzz\Client\Curl */
    protected $client;

    /** @var string */
    protected $credentials;

    /**
     * @var string
     * @see http://curl.haxx.se/docs/caextract.html
     */
    protected $certificatePath;

    /**
     * initialize the adapter
     * @param \Buzz\Browser $client
     * @param string $certificatePath
     */
    public function __construct(
        \Buzz\Client\Curl $client, $certificatePath = null
    ) {
        $this->client = $client;
        $this->certificatePath = $certificatePath;
    }

    /**
     * make a GET request
     * @param string $uri
     * @param string $resource
     * @param array $params
     * @return string|null
     */
    public function get($uri, $resource, array $params = array())
    {
        return $this->makeRequest(self::GET, $uri, $resource, $params);
    }

    /**
     * make a POST request
     * @param string $uri
     * @param string $resource
     * @param array $params
     * @return string|null
     */
    public function post($uri, $resource, array $params = array())
    {
        return $this->makeRequest(self::POST, $uri, $resource, $params);
    }

    /**
     * make an HTTP request
     * @param string $method
     * @param string $uri
     * @param string $resource
     * #param array $params
     * @return string|null
     */
    protected function makeRequest(
        $method, $uri, $resource, array $params = array()
    ) {

        $request = $this->createRequest($method, $resource, $uri);
        $request->setContent($params);

        $response = $this->createResponse();

        $this->client->send($request, $response, $params);

        if ($response->isOk()) {
            return $response->getContent();
        }

        return null;

    }

    /**
     * create a new instance of a request
     * @return \Buzz\Message\Request
     */
    protected function createRequest($method, $resource = '', $host = null)
    {
        return new \Buzz\Message\Request($method, '/' . $resource, $host);
    }

    /**
     * create a new instance of a response
     * @return \Buzz\Message\Response
     */
    protected function createResponse()
    {
        return new \Buzz\Message\Response();
    }

}
