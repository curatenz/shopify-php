# shopify-php

A simple [Shopify API](http://api.shopify.com/) client in PHP.

The canoncial repository for this stream of development is
[https://github.com/TeamOffshoot/shopify-php](https://github.com/TeamOffshoot/shopify-php)

## Requirements

* PHP 5.3 (or higher)
* ext-curl, ext-json
* kriswallsmith/buzz 0.10

## Development Requirements

* phpunit/phpunit 3.7

## Getting Started

Install shopify-php via [Composer](http://getcomposer.org/)

Create a `composer.json` file if you don't already have one in your projects
root directory and require shopify-php:

    {
      "require": {
        "offshoot/shopify-php": "1.0.x"
      }
    }

To learn more about Composer, including the complete installation process,
visit http://getcomposer.org/

## Usage

### Authentication

If you do not already have a Shopify API Permanent Access Token, you will need
you authenticate with the Shopify API first

TODO: info on HTTP Client

    $redirector = new \Shopify\Redirector\HeaderRedirector();

    $authenticate = new \Shopify\Api\AuthenticationGateway(
        $httpClient, $redirector
    );

    $authenticate->forShopName('mycoolshop')
        ->usingClientId('XXX1234567890') // get this from your Shopify Account
        ->withScope(array('write_products', 'read_orders'))
        ->andReturningTo("http://wherever.you/like")
        ->initiateLogin();

This will redirect your user to a Shopify login screen where they will need
to authenticate with their Shopify credentials. After doing that, Shopify will
perform a GET request to your redirect URI, that will look like:

    GET http://wherever.you/like?code=TEMP_TOKEN

Your application will need to capture the `code` query param from the request
and use that to get the permanent access token from Shopify

TODO: finish this off

### Interacting with the Shopify API

TODO: coming soon...

## Contributing

Contributions are welcome. Just fork the repository and send a pull request.
Please be sure to include test coverage with your pull request. You can learn
more about Pull Requests [here](https://help.github.com/articles/creating-a-pull-request)

In order to run the test suite, ensure that the development dependencies have
been installed via composer. Then from your command line, simple run:

    vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/

## License

This library is released under the [MIT License](https://github.com/TeamOffshoot/shopify-php/blob/master/LICENSE.txt)

## Acknowledgements

Thanks to [Sandeep Shetty](https://github.com/sandeepshetty/shopify_api) for
his development of the initial code base.
