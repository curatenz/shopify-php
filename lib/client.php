<?php

    namespace sandeepshetty\shopify_api;
    require 'vendor/autoload.php';


    function install_url($shop, $api_key)
    {
        return "http://$shop/admin/api/auth?api_key=$api_key";
    }

    function permission_url($shop, $api_key, $scope=array(), $redirect_uri='')
    {
        $scope = empty($scope) ? '' : '&scope='.implode(',', $scope);
        $redirect_uri = empty($redirect_uri) ? '' : '&redirect_uri='.urlencode($redirect_uri);
        return "https://$shop/admin/oauth/authorize?client_id=$api_key$scope$redirect_uri";
    }


    function oauth_access_token($shop, $api_key, $shared_secret, $code)
    {
        return _api('POST', "https://$shop/admin/oauth/access_token", NULL, array('client_id'=>$api_key, 'client_secret'=>$shared_secret, 'code'=>$code));
    }


    function client($shop, $shops_token, $api_key, $shared_secret, $private_app=false)
    {
        $password = $shops_token;
        $baseurl = "https://$shop/";

        return function ($method, $path, $params=array(), &$response_headers=array()) use ($baseurl, $shops_token)
        {
            $url = $baseurl.ltrim($path, '/');
            $query = in_array($method, array('GET','DELETE')) ? $params : array();
            $payload = in_array($method, array('POST','PUT')) ? stripslashes(json_encode($params)) : array();

            $request_headers = array();
            array_push($request_headers, "X-Shopify-Access-Token: $shops_token");
            if (in_array($method, array('POST','PUT'))) array_push($request_headers, "Content-Type: application/json; charset=utf-8");

            return _api($method, $url, $query, $payload, $request_headers, $response_headers);
        };
    }

    function _api($method, $url, $query='', $payload='', $request_headers=array(), &$response_headers=array())
    {
        try
        {
            $response = wcurl($method, $url, $query, $payload, $request_headers, $response_headers);
        }
        catch(WcurlException $e)
        {
            throw new CurlException($e->getMessage(), $e->getCode());
        }

        $response = json_decode($response, true);

        if (isset($response['errors']) or ($response_headers['http_status_code'] >= 400))
                throw new ApiException(compact('method', 'path', 'params', 'response_headers', 'response', 'shops_myshopify_domain', 'shops_token'));

        return (is_array($response) and !empty($response)) ? array_shift($response) : $response;
    }


    function calls_made($response_headers)
    {
        return _shop_api_call_limit_param(0, $response_headers);
    }


    function call_limit($response_headers)
    {
        return _shop_api_call_limit_param(1, $response_headers);
    }


    function calls_left($response_headers)
    {
        return call_limit($response_headers) - calls_made($response_headers);
    }


    function _shop_api_call_limit_param($index, $response_headers)
    {
        $params = explode('/', $response_headers['http_x_shopify_shop_api_call_limit']);
        return (int) $params[$index];
    }

    function legacy_token_to_oauth_token($shops_token, $shared_secret, $private_app=false)
    {
        return $private_app ? $secret : md5($shared_secret.$shops_token);
    }


    function legacy_baseurl($shop, $api_key, $password)
    {
        return "https://$api_key:$password@$shop/";

    }

?>
