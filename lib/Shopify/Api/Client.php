<?php

namespace Shopify\Api;

use Shopify\HttpClient;

class Client
{

    const SHOP_URI = 'https://%s.myshopify.com';
    CONST SHOP_API_CALL_LIMIT = 'http_x_shopify_shop_api_call_limit';

    /** @var string */
    protected $shopName;

    /**
     * the permanenet access token generated by Shopify
     * @var string
     */
    protected $accessToken;

    /**
     * the shared secret created by Shopify
     * @var string
     */
    protected $sharedSecret;

    /**
     * the http client used to make requests to the shopify api
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * initialize the API client
     * @param HttpClient $client
     */
    public function __construct(HttpClient $client)
    {
        $this->httpClient = $client;
    }

    /**
     * set the shop name
     * @param string $shopName
     */
    public function setShopName($shopName)
    {
        $this->shopName = $shopName;
    }

    /**
     * set the permanent access token returned by Shopify API
     * @param string $token
     */
    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }

    /**
     * set the shared secret
     * @param string
     */
    public function setClientSecret($secret)
    {
        $this->sharedSecret = $secret;
    }

    /**
     * get the base URI for the current shop
     * @return string
     */
    public function getShopUri()
    {
        return sprintf(self::SHOP_URI, $this->getShopName());
    }

    /**
     * make a GET request to the Shopify API
     * @param string $resource
     * @param array $params
     * @return \stdClass
     */
    public function get($resource, array $params = array())
    {
        return $this->makeApiRequest($resource, $params);
    }

    /**
     * make a POST request to the Shopify API
     * @param string $resource
     * @param array $data
     * @return \stdClass
     */
    public function post($resource, array $data = array())
    {
        return $this->makeApiRequest($resource, $data, HttpClient::POST);
    }

    /**
     * make a PUT request to the Shopify API
     * @param string $resource
     * @param array $data
     * @return \stdClass
     */
    public function put($resource, array $data = array())
    {
        return $this->makeApiRequest($resource, $data, HttpClient::PUT);
    }

    /**
     * make a POST request to the Shopify API
     * @param string $resource
     * @param array $data
     * @return \stdClass
     */
    public function delete($resource, array $data = array())
    {
        return $this->makeApiRequest($resource, $data, HttpClient::DELETE);
    }

    /**
     * generate the signature as required by shopify
     * @param array $params
     * @return string
     */
    public function generateSignature(array $params)
    {

        // Collect the URL parameters into an array of elements of the format
        // "$parameter_name=$parameter_value"

        $calculated = array();

        foreach ($params as $key => $value) {
            $calculated[] = $key . "=" . $value;
        }

        // Sort the key/value pairs in the array
        sort($calculated);

        // Join the array elements into a string
        $calculated = implode('', $calculated);

        // Final calculated_signature to compare against
        return md5($this->getClientSecret() . $calculated);

    }

    /**
     * validate the signature on the supplied query parameters
     * @return boolean
     */
    public function validateSignature(array $params)
    {

        $this->assertRequestParamIsNotNull(
            $params, 'signature', 'Expected signature in query params'
        );

        $signature = $params['signature'];
        unset($params['signature']);

        return $this->generateSignature($params) === $signature;

    }

    /**
     * returns true if the supplied request params are valid
     * @return boolean
     */
    public function isValidRequest(array $params)
    {

        $this->assertRequestParamIsNotNull(
            $params, 'timestamp', 'Expected timestamp in query params'
        );

        $requestTimestamp = $params['timestamp'];
        $secondsPerDay = 24 * 60 * 60;
        $olderThanOneDay = $requestTimestamp < (time() - $secondsPerDay);

        return ($olderThanOneDay) ? false : $this->validateSignature($params);

    }

    /**
     * get the number of calls made to the shopify api
     * @return integer
     */
    public function getNumberOfCallsMade(array $headers)
    {
        return $this->getCallLimitParam(0, $headers);
    }

    /**
     * get the total number of calls that can be made to the shopify api
     * @return integer
     */
    public function getCallLimit(array $headers)
    {
        return $this->getCallLimitParam(1, $headers);
    }

    /**
     * get the available number of remaining calls that can be made to the
     * shopify api
     * @return integer
     */
    public function getNumberOfCallsRemaining(array $headers)
    {
        return $this->getCallLimit($headers)
        - $this->getNumberOfCallsMade($headers);
    }

    /**
     * get the http_x_shopify_shop_api_call_limit header from the response
     * and parse it into an array to get access to the specific values
     * @return integer
     */
    protected function getCallLimitParam($index, array $headers)
    {

        $shopifyShopApiCallLimit = array_key_exists(
            self::SHOP_API_CALL_LIMIT,
            $headers
        ) ? $headers[self::SHOP_API_CALL_LIMIT] : '0/0';

        $params = explode('/', $shopifyShopApiCallLimit);
        return array_key_exists($index, $params)
            ? (int) $params[$index] : 0;

    }

    /**
     * make a generic request to the api
     * @param string $resource
     * @param array $params
     * @param string $method
     * @return \stdClass
     */
    protected function makeApiRequest(
        $resource, array $params = array(), $method = HttpClient::GET
    ) {

        $uri = $this->getShopUri() . '/' . ltrim($resource, '/');

        $this->getHttpClient()->setAccessToken($this->getAccessToken());

        switch ($method) {
            case HttpClient::GET:
                $response = $this->getHttpClient()->get($uri, $params);
                break;
            case HttpClient::POST:
                $data = json_encode($params);
                $response = $this->getHttpClient()->post($uri, $data);
                break;
            case HttpClient::PUT:
                $data = json_encode($params);
                $response = $this->getHttpClient()->put($uri, $data);
                break;
            case HttpClient::DELETE:
                $response = $this->getHttpClient()->delete($uri, $params);
                break;
//            default:
//                throw new \RuntimeException(
//                    'Currently only "GET" and "POST" are supported. "PUT" and '
//                    . '"DELETE" functionality is currently under development'
//                );
        }

        $response = json_decode($response);

        if (isset($response->errors)) {
            // Errors can sometimes be an array. Take this into account.
            $error = is_string($response->errors) ? $response->errors : current(array_flatten($response->errors));
            throw new \RuntimeException($error);
        }

        return $response;

    }

    /**
     * get the HTTP Client
     * @return HttpClient
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * get the shopify permanent access token
     * @return string
     */
    protected function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * get the shop name
     * @return string
     */
    protected function getShopName()
    {
        return $this->shopName;
    }

    /**
     * get the shared secret
     * @return string
     */
    protected function getClientSecret()
    {
        return $this->sharedSecret;
    }

    /**
     * throws an exception if the param in the supplied request is null
     * @param array $params
     * @param string $key
     * @param string $message
     * @throws RequestException
     */
    protected function assertRequestParamIsNotNull(
        array $params, $key, $message
    ) {

        $value = array_key_exists($key, $params)
            ? $params[$key] : null;

        if (is_null($value)) {
            $e = new RequestException($message);
            $e->setQueryParams($params);
            throw $e;
        }

    }

}
