<?php

namespace Shopify;

interface Redirector
{

    /**
     * rediect to the supplied uri
     * @param string $uri
     * @return mixed
     */
    public function redirect($uri);

}
