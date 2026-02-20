<?php

declare(strict_types=1);

namespace Ipstack\Tests;

use Ipstack\Exception\InvalidResponseException;
use Ipstack\Exception\TransportException;
use Ipstack\Transport\Psr18Transport;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class Psr18TransportTest extends TestCase
{
    public function testGetBuildsRequestWithQueryAndReturnsDecodedArray(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $requests = $this->createMock(RequestFactoryInterface::class);
        $http = $this->createMock(ClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $stream = $this->createMock(StreamInterface::class);

        $requests->expects($this->once())
            ->method('createRequest')
            ->with(
                'GET',
                'https://api.ipstack.com/8.8.8.8?access_key=KEY&language=en'
            )
            ->willReturn($request);

        $http->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);
        $stream->method('__toString')->willReturn('{"ip":"8.8.8.8"}');

        $transport = new Psr18Transport($http, $requests);
        $data = $transport->get('https://api.ipstack.com/8.8.8.8', [
            'access_key' => 'KEY',
            'language' => 'en',
        ]);

        $this->assertSame('8.8.8.8', $data['ip']);
    }

    public function testGetThrowsTransportExceptionOnNonSuccessfulStatus(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $requests = $this->createMock(RequestFactoryInterface::class);
        $http = $this->createMock(ClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $requests->method('createRequest')->willReturn($request);
        $http->method('sendRequest')->willReturn($response);
        $response->method('getStatusCode')->willReturn(403);

        $transport = new Psr18Transport($http, $requests);

        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('Unexpected HTTP status: 403');

        $transport->get('https://api.ipstack.com/8.8.8.8', ['access_key' => 'KEY']);
    }

    public function testGetThrowsInvalidResponseExceptionWhenJsonIsInvalid(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $requests = $this->createMock(RequestFactoryInterface::class);
        $http = $this->createMock(ClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $stream = $this->createMock(StreamInterface::class);

        $requests->method('createRequest')->willReturn($request);
        $http->method('sendRequest')->willReturn($response);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($stream);
        $stream->method('__toString')->willReturn('not-json');

        $transport = new Psr18Transport($http, $requests);

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Invalid JSON response');

        $transport->get('https://api.ipstack.com/8.8.8.8', ['access_key' => 'KEY']);
    }

    public function testGetWrapsUnderlyingClientThrowable(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $requests = $this->createMock(RequestFactoryInterface::class);
        $http = $this->createMock(ClientInterface::class);

        $requests->method('createRequest')->willReturn($request);
        $http->method('sendRequest')->willThrowException(new \RuntimeException('network down'));

        $transport = new Psr18Transport($http, $requests);

        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('HTTP request failed: network down');

        $transport->get('https://api.ipstack.com/8.8.8.8', ['access_key' => 'KEY']);
    }
}
