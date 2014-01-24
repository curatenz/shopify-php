<?php

namespace Shopify\Api;

class Exception extends \Exception
{

    /** @var array */
    protected $info;

    /**
     * initialize the exception with the supplied info
     * @param array $info
     */
    public function __construct(array $info)
    {

        $this->info = $info;

        parent::__construct(
            $this->getResponseHeader('http_status_message'),
            $this->getResponseHeader('http_status_code')
        );

    }

    /**
     * return the information originally supplied to the exception
     * constructor
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * get a response header from the info array
     * @param string $key
     * @return string|null
     */
    protected function getResponseHeader($key)
    {

        $info = $this->getInfo();
        $headers = array_key_exists('response_headers', $info)
            ? $info['response_headers'] : null;

        if (!is_null($headers)) {
            return array_key_exists($key, $headers)
                ? $headers[$key] : null;
        }

        return null;

    }

}
