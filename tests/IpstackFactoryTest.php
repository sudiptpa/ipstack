<?php

declare(strict_types=1);

namespace Ipstack\Tests;

use Ipstack\Factory\IpstackFactory;
use Ipstack\Tests\Fakes\FakeTransport;
use PHPUnit\Framework\TestCase;

final class IpstackFactoryTest extends TestCase
{
    public function testBuildThrowsWhenAccessKeyIsMissing(): void
    {
        $factory = (new IpstackFactory())
            ->withTransport(new FakeTransport(['ip' => '8.8.8.8']));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Access key is required.');

        $factory->build();
    }

    public function testBuildThrowsWhenTransportIsMissing(): void
    {
        $factory = (new IpstackFactory())
            ->withAccessKey('KEY');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No transport configured.');

        $factory->build();
    }

    public function testBuildCreatesClientWhenConfigured(): void
    {
        $factory = (new IpstackFactory())
            ->withAccessKey('KEY')
            ->withTransport(new FakeTransport([
                'ip' => '8.8.8.8',
                'country_name' => 'United States',
            ]));

        $client = $factory->build();
        $result = $client->lookup('8.8.8.8');

        $this->assertSame('8.8.8.8', $result->ip);
        $this->assertSame('United States', $result->country->name);
    }
}
