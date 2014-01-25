<?php

namespace Shopify\Api;

use Shopify\HttpClient;
use Shopify\Redirector;

class AuthenticationGateway
{

    const AUTHORIZATION_URI = 'https://%s.myshopify.com/admin/oauth/authorize';

    /** @var string */
    protected $shopName;

    /** @var string */
    protected $clientId;

    /** @var array */
    protected $scope;

    /** @var string */
    protected $redirectUri;

    /** @var HttpClient */
    protected $httpClient;

    /** @var Redirector */
    protected $redirector;

    /**
     * initialize the authentication gateway
     * @param HttpClient $httpClient
     * @param Redirector $redirector
     */
    public function __construct(HttpClient $httpClient, Redirector $redirector)
    {
        $this->httpClient = $httpClient;
        $this->redirector = $redirector;
    }

    /**
     * a simple DSL on top of setting the shop name
     * @param string $shopName
     * @return AuthenticationGateway
     */
    public function forShopName($shopName)
    {
        $this->setShopName($shopName);
        return $this;
    }

    /**
     * a simple DSL on top of setting the client ID
     * @param string $clientId
     * @return AuthenticationGateway
     */
    public function usingClientId($clientId)
    {
        $this->setClientId($clientId);
        return $this;
    }

    /**
     * a simple DSL on top of setting the permission scope
     * @param array $scope
     * @return AuthenticationGateway
     */
    public function withScope(array $scope)
    {
        $this->setScope($scope);
        return $this;
    }

    /**
     * a simple DSL on top of setting the redirect URI
     * @param string $redirectUri
     * @return AuthenticationGateway
     */
    public function andReturningTo($redirectUri)
    {
        $this->setRedirectUri($redirectUri);
        return $this;
    }

    /**
     * initiate the login process
     */
    public function initiateLogin()
    {

        if (!$this->canInitiateLogin()) {
            throw new \RuntimeException(
                'Unable to initiate login'
            );
        }

        $uri = $this->getAuthenticationUri();
        return $this->redirector->redirect($uri);

    }

    /**
     * build the shopify authentication uri that users are
     * forwarded to for authentication
     * @return string
     */
    public function getAuthenticationUri()
    {

        $authorizeUri = sprintf(self::AUTHORIZATION_URI, $this->getShopName());

        $uriParams = array(
            'client_id' => $this->getClientId(),
            'scope' => $this->getPreparedScope(),
        );

        if ($this->getRedirectUri()) {
            $uriParams['redirect_uri'] = $this->getRedirectUri();
        }

        return $authorizeUri . '?' . http_build_query($uriParams);

    }

    /**
     * assert that it is possible to proceed with initiating the login
     * @return boolean
     */
    protected function canInitiateLogin()
    {

        if (!$this->canBuildAuthenticationUri()) {
            throw new \RuntimeException(
                'Cannot build authentication uri, dependencies are missing'
            );
        }

        return true;

    }

    /**
     * assert that it is possible to build the authentication uri
     * @return boolean
     */
    protected function canBuildAuthenticationUri()
    {
        return $this->getClientId()
            && $this->getShopName()
            && $this->getPreparedScope();
    }

    /**
     * set the shop name
     * @param string $shopName
     */
    protected function setShopName($shopName)
    {
        $this->shopName = $shopName;
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
     * set the client ID
     * @param string $clientId
     */
    protected function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * get the client ID
     * @return string
     */
    protected function getClientId()
    {
        return $this->clientId;
    }

    /**
     * set the permission scope
     * @param array $scope
     */
    protected function setScope(array $scope)
    {
        $this->scope = $scope;
    }

    /**
     * get the scope as a comma separated string
     * @return string
     */
    protected function getPreparedScope()
    {
        return join(',', $this->scope);
    }

    /**
     * set the redirect URI
     * @param string $redirectUri
     */
    protected function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * get the redirect uri
     * @return string
     */
    protected function getRedirectUri()
    {
        return $this->redirectUri;
    }

}
