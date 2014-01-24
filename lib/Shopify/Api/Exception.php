<?php

namespace Shopify\Api;

class Exception extends \Exception
{

    protected $info;

    public function __construct(array $info)
    {

        $this->info = $info;

        parent::__construct(
            $info['response_headers']['http_status_message'],
            $info['response_headers']['http_status_code']
        );

    }

    public function getInfo()
    {
        $this->info;
    }
}
