## A simple implementation for IP to Location with http://freegeoip.net an open source platform.

[![Build Status](https://travis-ci.org/sudiptpa/geoip.svg?branch=master)](https://travis-ci.org/sudiptpa/geoip)
[![StyleCI](https://styleci.io/repos/115319108/shield?branch=master)](https://styleci.io/repos/115319108)

http://freegeoip.net provides a public HTTP API for software developers to search the geolocation of IP addresses. It uses a database of IP addresses that are associated to cities along with other relevant information like time zone, latitude and longitude.

You're allowed up to 15,000 queries per hour by default. Once this limit is reached, all of your requests will result in HTTP 403, forbidden, until your quota is cleared.

The freegeoip web server is free and open source so if the public service limit is a problem for you, download it and run your own instance.

### Installation

You can install the package via composer: [Composer](http://getcomposer.org/).

```
composer require sudiptpa/geoip
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

### Usage

This package only supports `json` format for now.

Here are a few examples on how you can use the package:

```php
  $geo = new Sujip\GeoIp\GeoIp($ip);
  
  $geo->country();
  
  $geo->city();
  
  $geo->region();
  
```
Also have a look in the [source code of `Sujip\GeoIp\GeoIp`](https://github.com/sudiptpa/geoip/blob/master/src/GeoIp.php) to discover the methods you can use.

### Changelog

Please see [CHANGELOG](https://github.com/sudiptpa/geoip/blob/master/CHANGELOG.md) for more information what has changed recently.

### Contributing

Contributions are **welcome** and will be fully **credited**.

Contributions can be made via a Pull Request on [Github](https://github.com/sudiptpa/geoip).



### Testing

```
  composer test
 ```

### Support

If you are having general issues with the package, feel free to drop me and email [sudiptpa@gmail.com](mailto:sudiptpa@gmail.com)

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/sudiptpa/geoip/issues),
or better yet, fork the library and submit a pull request.
