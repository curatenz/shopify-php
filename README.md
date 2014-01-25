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

Coming soon...

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
