## A simple package for IP to Location implementation with PHP using real-time API service through https://ipstack.com.

https://ipstack.com provides a public HTTP API for software developers to search the geolocation of IP addresses. It uses a database of IP addresses that are associated to cities along with other relevant information like time zone, latitude and longitude.

You're allowed up to 10,000 queries per month by default. Once this limit is reached, all of your requests will result in HTTP 403, forbidden, until your quota is cleared.

The ipstack is an API service which enable you to locate and identify website visitors at a stage before any data is entered into your system. The data received from the API can be used to enhance user experiences based on location data and assess risks and potential threats to your web application in time.

### Installation

You can install the package via composer: [Composer](http://getcomposer.org/).

```
composer require sudiptpa/ipstack
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

### Usage

This package only supports `json` format for now.

Here are a few examples on how you can use the package:

```php
  $geo = new Sujip\Ipstack\Ipstack($ip);

  $geo->country();

  $geo->city();

  $geo->region();

```
Also have a look in the [source code of `Sujip\Ipstack\Ipstack`](https://github.com/sudiptpa/ipstack/blob/master/src/Ipstack.php) to discover the methods you can use.

### Changelog

Please see [CHANGELOG](https://github.com/sudiptpa/ipstack/blob/master/CHANGELOG.md) for more information what has changed recently.

### Contributing

Contributions are **welcome** and will be fully **credited**.

Contributions can be made via a Pull Request on [Github](https://github.com/sudiptpa/geoip).



### Testing

```
  composer test
 ```

### Support

If you are having general issues with the package, feel free to drop me and email [sudiptpa@gmail.com](mailto:sudiptpa@gmail.com)

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/sudiptpa/ipstack/issues),
or better yet, fork the library and submit a pull request.
