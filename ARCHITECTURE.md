# Architecture

## Overview

`sudiptpa/ipstack` is a framework-agnostic PHP SDK for the ipstack geolocation API.
The package is organized around a small client core, explicit models, and pluggable transport.

## Project Map

```text
src/
  Ipstack.php                     Static entrypoint for factory creation
  Factory/
    IpstackFactory.php            Fluent builder for client configuration
  Client/
    IpstackClient.php             Main API client (lookup/check/bulk)
    Config.php                    Runtime client configuration
    Endpoint.php                  Endpoint constants/helpers
    Options.php                   Query options (fields, language, modules)
  Transport/
    TransportInterface.php        HTTP abstraction contract
    Psr18Transport.php            Default PSR-18 transport adapter
  Mapper/
    IpstackResultMapper.php       Array payload -> typed models
  Model/
    IpstackResult.php             Single lookup result
    IpstackCollection.php         Bulk response collection
    Country.php
    Region.php
    Location.php
    Security.php
    Connection.php
    Routing.php
    Attributes/MapFrom.php        Field mapping attribute for hydration
  Exception/
    IpstackException.php          Base domain exception
    ApiErrorException.php         API error wrapper
    InvalidResponseException.php  Malformed/unexpected response
    TransportException.php        Transport-layer failure
    RateLimitException.php
    InvalidFieldsException.php
    TooManyIpsException.php
    BatchNotSupportedException.php
tests/
  IpstackClientTest.php
  IpstackClientEdgeCaseTest.php
  IpstackFactoryTest.php
  Psr18TransportTest.php
  Fakes/FakeTransport.php
```

## Request Lifecycle

1. `Ipstack::factory()` creates `IpstackFactory`.
2. Factory receives access key and transport strategy (`withPsr18()` or `withTransport()`).
3. `build()` returns `IpstackClient`.
4. Client composes endpoint + query options and delegates the HTTP call to `TransportInterface`.
5. Response array is validated and mapped by `IpstackResultMapper`.
6. Caller receives typed models (`IpstackResult` or `IpstackCollection`) or a typed exception.

## Error Model

- `TransportException` for networking/transport failures.
- `InvalidResponseException` for malformed payloads.
- API-level errors are normalized into `ApiErrorException` and specific known subclasses:
  - `RateLimitException`
  - `InvalidFieldsException`
  - `TooManyIpsException`
  - `BatchNotSupportedException`

## Extension Points

- Implement `TransportInterface` to integrate any HTTP stack.
- Extend usage via `Options` for query modules (`security`, `hostname`, `language`, `fields`).
- Use `IpstackResultMapper` as the single mapping layer when adding new ipstack response attributes.

## Testing Strategy

- Unit tests focus on client behavior, factory configuration, edge-case handling, and transport behavior.
- `FakeTransport` enables deterministic tests without real network calls.
- CI runs PHPUnit and PHPStan for regression and static safety.

## Contributor Notes

- Keep public API additive where possible.
- Add tests for every behavior change and update `README.md` if usage changes.
- Keep core package dependency-light and framework-agnostic.
