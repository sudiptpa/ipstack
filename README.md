# ipstack PHP Client

[![CI](https://github.com/sudiptpa/ipstack/actions/workflows/ci.yml/badge.svg)](https://github.com/sudiptpa/ipstack/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/packagist/v/sudiptpa/ipstack)](https://packagist.org/packages/sudiptpa/ipstack)
[![Downloads](https://img.shields.io/packagist/dt/sudiptpa/ipstack)](https://packagist.org/packages/sudiptpa/ipstack)
[![License](https://img.shields.io/packagist/l/sudiptpa/ipstack)](https://packagist.org/packages/sudiptpa/ipstack)
[![PHP Version](https://img.shields.io/packagist/php-v/sudiptpa/ipstack)](https://packagist.org/packages/sudiptpa/ipstack)

A modern, PSR-based PHP client for the [ipstack](https://ipstack.com) API.

## Highlights

- PSR-18 transport architecture
- Factory-based client construction
- Typed result models (`IpstackResult`, `Country`, `Region`, etc.)
- Bulk lookup support (up to 50 IPs per request)
- Domain exceptions mapped from ipstack API error codes

## Requirements

- PHP `8.3` to `8.5` (`>=8.3 <8.6`)
- `psr/http-client`
- `psr/http-factory`
- `psr/http-message`
- A concrete PSR-18 client + PSR-17 factory implementation (for example `symfony/http-client` + `nyholm/psr7`)

## Installation

```bash
composer require sudiptpa/ipstack
```

Optional (for the PSR-18 quick-start adapter stack shown below):

```bash
composer require symfony/http-client nyholm/psr7
```

## Architecture

Core layers:

- `Ipstack::factory()` configures and builds the client
- `IpstackClient` runs lookup operations
- `TransportInterface` abstracts HTTP transport (`Psr18Transport` included)
- `IpstackResultMapper` maps API payloads to typed models

Main entry points:

- `Ipstack\\Ipstack`
- `Ipstack\\Factory\\IpstackFactory`
- `Ipstack\\Client\\IpstackClient`
- `Ipstack\\Client\\Options`

## Quick Start (PSR-18)

```php
<?php

declare(strict_types=1);

use Ipstack\Ipstack;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;

$client = Ipstack::factory()
    ->withAccessKey('YOUR_ACCESS_KEY')
    ->withPsr18(new Psr18Client(), new Psr17Factory())
    ->build();

$result = $client->lookup('8.8.8.8');

echo $result->formatted();
```

## Full Usage Examples

### 1) Reusable bootstrap

```php
use Ipstack\Client\IpstackClient;
use Ipstack\Ipstack;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;

function ipstackClient(string $accessKey): IpstackClient
{
    return Ipstack::factory()
        ->withAccessKey($accessKey)
        ->withPsr18(new Psr18Client(), new Psr17Factory())
        ->build();
}
```

### 2) Override base URL (advanced)

```php
$client = Ipstack::factory()
    ->withAccessKey('YOUR_ACCESS_KEY')
    ->withBaseUrl('https://api.ipstack.com')
    ->withPsr18(new Psr18Client(), new Psr17Factory())
    ->build();
```

### 3) Single IP lookup + options

```php
use Ipstack\Client\Options;

$options = Options::create()
    ->fields(['ip', 'country_name', 'region_name', 'city', 'zip'])
    ->language('en')
    ->security(true)
    ->hostname(true);

$result = $client->lookup('8.8.8.8', $options);

echo $result->ip . PHP_EOL;
echo $result->formatted() . PHP_EOL;
echo ($result->country->name ?? 'unknown') . PHP_EOL;
```

### 4) Requester IP (`/check`)

```php
$result = $client->lookupRequester();

echo $result->ip . PHP_EOL;
```

### 5) Bulk lookup

```php
$results = $client->lookupBulk(['8.8.8.8', '1.1.1.1']);

echo 'count=' . count($results) . PHP_EOL;

foreach ($results as $row) {
    echo $row->ip . ' => ' . $row->formatted() . PHP_EOL;
}
```

Notes:

- Empty list returns an empty `Ipstack\\Model\\IpstackCollection`
- More than 50 IPs throws `Ipstack\\Exception\\IpstackException`

### 6) Access raw payload and JSON output

```php
$result = $client->lookup('8.8.8.8');

$raw = $result->raw();
$json = json_encode($result, JSON_PRETTY_PRINT);

echo $json . PHP_EOL;
```

### 7) Custom transport (local smoke test)

```php
use Ipstack\Ipstack;
use Ipstack\Transport\TransportInterface;

final class DemoTransport implements TransportInterface
{
    public function get(string $url, array $query): array
    {
        return [
            'ip' => '8.8.8.8',
            'country_name' => 'United States',
            'country_code' => 'US',
            'region_name' => 'California',
            'region_code' => 'CA',
            'city' => 'Mountain View',
            'zip' => '94035',
        ];
    }
}

$client = Ipstack::factory()
    ->withAccessKey('DUMMY_KEY')
    ->withTransport(new DemoTransport())
    ->build();

echo $client->lookup('8.8.8.8')->formatted();
```

### 8) Typed exception handling

```php
use Ipstack\Exception\ApiErrorException;
use Ipstack\Exception\BatchNotSupportedException;
use Ipstack\Exception\InvalidFieldsException;
use Ipstack\Exception\InvalidResponseException;
use Ipstack\Exception\IpstackException;
use Ipstack\Exception\RateLimitException;
use Ipstack\Exception\TooManyIpsException;
use Ipstack\Exception\TransportException;

try {
    $result = $client->lookup('8.8.8.8');
} catch (RateLimitException $e) {
    // 104
} catch (InvalidFieldsException $e) {
    // 301
} catch (TooManyIpsException $e) {
    // 302
} catch (BatchNotSupportedException $e) {
    // 303
} catch (TransportException | InvalidResponseException $e) {
    // network/HTTP/JSON transport failure
} catch (ApiErrorException $e) {
    // other API-side errors
} catch (IpstackException $e) {
    // any other library-level exception
}
```

## Models

`lookup*()` returns `Ipstack\\Model\\IpstackResult` with nested typed objects:

- `country` (`Country`)
- `region` (`Region`)
- `location` (`Location|null`)
- `connection` (`Connection|null`)
- `security` (`Security|null`)
- `routing` (`Routing|null`)

Helpers:

- `$result->formatted()` returns a readable location string
- `$result->raw()` returns original decoded payload
- `json_encode($result)` serializes raw payload (`JsonSerializable`)

## Error Model

All package exceptions extend `Ipstack\\Exception\\IpstackException`.

Transport/response exceptions:

- `TransportException`
- `InvalidResponseException`

API exceptions:

- `ApiErrorException` (base)
- `RateLimitException` for code `104`
- `InvalidFieldsException` for code `301`
- `TooManyIpsException` for code `302`
- `BatchNotSupportedException` for code `303`

## Real API Smoke Test

Use an environment variable and run this one-liner:

```bash
IPSTACK_ACCESS_KEY=your_key php -r 'require "vendor/autoload.php"; $c=Ipstack\Ipstack::factory()->withAccessKey(getenv("IPSTACK_ACCESS_KEY"))->withPsr18(new Symfony\Component\HttpClient\Psr18Client(), new Nyholm\Psr7\Factory\Psr17Factory())->build(); echo $c->lookup("8.8.8.8")->formatted(), PHP_EOL;'
```

## Troubleshooting

- `InvalidArgumentException: Access key is required.`
  - Call `->withAccessKey('...')` before `->build()`.
- `InvalidArgumentException: No transport configured...`
  - Configure either `->withPsr18(...)` or `->withTransport(...)`.
- `TransportException: Unexpected HTTP status ...`
  - Check network access, endpoint, and ipstack key validity.
- `InvalidResponseException: Invalid JSON response`
  - Usually an upstream/proxy response issue; inspect raw HTTP response.
- `RateLimitException` (`104`)
  - Monthly/request quota reached on your ipstack account.

## API Plan Notes

Some ipstack fields/features may depend on plan tier (for example `security`, `hostname`, and parts of connection/routing data). If a field is unavailable on your plan, ipstack may omit it or return an API error.

## Breaking Changes

Compared to the legacy API style:

- `Sujip\\Ipstack\\Ipstack` is replaced by `Ipstack\\Ipstack`
- Constructor-style usage is replaced by factory-style configuration
- Legacy root convenience accessors are replaced by typed result objects from lookup methods
- `secure()` is no longer the documented toggle pattern
- Transport wiring is explicit and PSR-based
- Bulk lookup is now `lookupBulk(array $ips)` with a strict max of 50 IPs/request

## Migration Guide (v1 -> v2)

Use this quick mapping to migrate legacy usage to the current API.

| v1 (legacy) | v2 (current) |
| --- | --- |
| `new Sujip\\Ipstack\\Ipstack($ip)` | Build once, then call lookup: `Ipstack::factory()->withAccessKey(...)->withPsr18(...)->build()->lookup($ip)` |
| `new Sujip\\Ipstack\\Ipstack($ip, $apiKey)` | `Ipstack::factory()->withAccessKey($apiKey)->withPsr18(...)->build()` |
| `$ipstack->country()` | `$client->lookup($ip)->country->name` |
| `$ipstack->region()` | `$client->lookup($ip)->region->name` |
| `$ipstack->city()` | `$client->lookup($ip)->city` |
| `$ipstack->formatted()` | `$client->lookup($ip)->formatted()` |
| `$ipstack->secure()` | Use configured base URL + transport: `withBaseUrl(...)` + `withPsr18(...)` |
| N/A (or ad-hoc loops) | Native bulk call: `$client->lookupBulk([$ip1, $ip2])` |
| Generic runtime/API failures | Typed exception model (`RateLimitException`, `InvalidFieldsException`, `TransportException`, etc.) |

### Suggested migration steps

1. Replace direct constructor usage with factory-based client construction.
2. Create one reusable `IpstackClient` service and inject it where needed.
3. Replace legacy convenience getters with `lookup()` result model access.
4. Add typed exception handling around lookup calls.
5. Add a smoke test (local fake transport + real API env test) before release.

## Quality Gates

CI runs the following checks across PHP `8.3`, `8.4`, and `8.5`:

- `composer test`
- `composer stan` (on `prefer-stable` matrix jobs)

## Development

```bash
composer test
composer stan
composer rector
composer rector:check
```

## Changelog

See `CHANGELOG.md` for release notes (latest hardening release: `v2.1.0`).

## Author

- Sujip Thapa (`sudiptpa@gmail.com`)

## License

MIT. See `LICENSE`.
