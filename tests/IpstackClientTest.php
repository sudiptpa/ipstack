<?php

declare(strict_types=1);

namespace Ipstack\Tests;

use Ipstack\Client\Config;
use Ipstack\Client\Endpoint;
use Ipstack\Client\IpstackClient;
use Ipstack\Client\Options;
use Ipstack\Exception\ApiErrorException;
use Ipstack\Mapper\IpstackResultMapper;
use Ipstack\Tests\Fakes\FakeTransport;
use PHPUnit\Framework\TestCase;

final class IpstackClientTest extends TestCase
{
    public function testLookupMapsModels(): void
    {
        $fake = new FakeTransport([
            'ip' => '8.8.8.8',
            'country_name' => 'United States',
            'country_code' => 'US',
            'region_name' => 'California',
            'region_code' => 'CA',
            'city' => 'Mountain View',
            'zip' => '94035',
            'security' => [
                'is_proxy' => false,
                'proxy_level' => 'none'
            ],
            'connection' => [
                'asn' => 15169,
                'isp' => 'Google LLC',
                'tld' => 'com'
            ],
            'msa' => 'San Francisco-Oakland-Berkeley'
        ]);

        $client = new IpstackClient(
            new Config('KEY'),
            new Endpoint('https://api.ipstack.com'),
            $fake,
            new IpstackResultMapper()
        );

        $result = $client->lookup('8.8.8.8', Options::create()->security());

        $this->assertSame('United States', $result->country->name);
        $this->assertSame('California', $result->region->name);
        $this->assertSame('Mountain View, 94035, California, United States', $result->formatted());
        $this->assertSame(15169, $result->connection?->asn);
        $this->assertSame('none', $result->security?->proxyLevel);
    }

    public function testApiErrorThrows(): void
    {
        $fake = new FakeTransport([
            'error' => ['code' => 101, 'info' => 'Invalid access key']
        ]);

        $client = new IpstackClient(
            new Config('BAD'),
            new Endpoint('https://api.ipstack.com'),
            $fake,
            new IpstackResultMapper()
        );

        $this->expectException(ApiErrorException::class);
        $client->lookup('8.8.8.8');
    }
}
