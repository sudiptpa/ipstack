<?php

declare(strict_types=1);

namespace Ipstack\Tests;

use Ipstack\Client\Config;
use Ipstack\Client\Endpoint;
use Ipstack\Client\IpstackClient;
use Ipstack\Exception\BatchNotSupportedException;
use Ipstack\Exception\InvalidFieldsException;
use Ipstack\Exception\IpstackException;
use Ipstack\Exception\TooManyIpsException;
use Ipstack\Mapper\IpstackResultMapper;
use Ipstack\Tests\Fakes\FakeTransport;
use PHPUnit\Framework\TestCase;

final class IpstackClientEdgeCaseTest extends TestCase
{
    public function testLookupRequesterMapsResponse(): void
    {
        $client = new IpstackClient(
            new Config('KEY'),
            new Endpoint('https://api.ipstack.com'),
            new FakeTransport([
                'ip' => '127.0.0.1',
                'country_name' => 'Local',
                'region_name' => 'Loopback',
            ]),
            new IpstackResultMapper()
        );

        $result = $client->lookupRequester();

        $this->assertSame('127.0.0.1', $result->ip);
        $this->assertSame('Local', $result->country->name);
    }

    public function testLookupBulkReturnsEmptyCollectionForEmptyInput(): void
    {
        $client = new IpstackClient(
            new Config('KEY'),
            new Endpoint('https://api.ipstack.com'),
            new FakeTransport(['ip' => 'unused']),
            new IpstackResultMapper()
        );

        $results = $client->lookupBulk([]);

        $this->assertCount(0, $results);
    }

    public function testLookupBulkThrowsWhenMoreThanFiftyIpsAreProvided(): void
    {
        $client = new IpstackClient(
            new Config('KEY'),
            new Endpoint('https://api.ipstack.com'),
            new FakeTransport(['ip' => 'unused']),
            new IpstackResultMapper()
        );

        $ips = [];
        for ($i = 0; $i < 51; $i++) {
            $ips[] = "203.0.113.{$i}";
        }

        $this->expectException(IpstackException::class);
        $this->expectExceptionMessage('Bulk lookup supports up to 50 IPs per request.');

        $client->lookupBulk($ips);
    }

    public function testLookupBulkSkipsNonArrayRowsAndMapsArrayRows(): void
    {
        $client = new IpstackClient(
            new Config('KEY'),
            new Endpoint('https://api.ipstack.com'),
            new FakeTransport([
                [
                    'ip' => '8.8.8.8',
                    'country_name' => 'United States',
                ],
                'ignore-me',
                [
                    'ip' => '1.1.1.1',
                    'country_name' => 'Australia',
                ],
            ]),
            new IpstackResultMapper()
        );

        $results = $client->lookupBulk(['8.8.8.8', '1.1.1.1']);

        $this->assertCount(2, $results);
        $this->assertSame('8.8.8.8', $results->all()[0]->ip);
        $this->assertSame('1.1.1.1', $results->all()[1]->ip);
    }

    /**
     * @dataProvider apiErrorInBulkProvider
     * @param class-string<\Throwable> $class
     */
    public function testLookupBulkMapsTypedApiErrors(int $code, string $class): void
    {
        $client = new IpstackClient(
            new Config('KEY'),
            new Endpoint('https://api.ipstack.com'),
            new FakeTransport([
                'error' => ['code' => $code, 'info' => 'error'],
            ]),
            new IpstackResultMapper()
        );

        $this->expectException($class);

        $client->lookupBulk(['8.8.8.8']);
    }

    /**
     * @return array<string,array{int,class-string<\Throwable>}>
     */
    public static function apiErrorInBulkProvider(): array
    {
        return [
            'invalid-fields' => [301, InvalidFieldsException::class],
            'too-many-ips' => [302, TooManyIpsException::class],
            'batch-not-supported' => [303, BatchNotSupportedException::class],
        ];
    }
}
