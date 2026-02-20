<?php

declare(strict_types=1);

namespace Ipstack\Transport;

use Ipstack\Exception\InvalidResponseException;
use Ipstack\Exception\TransportException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final class Psr18Transport implements TransportInterface
{
    public function __construct(
        private readonly ClientInterface $http,
        private readonly RequestFactoryInterface $requests
    ) {}

    /**
     * @param array<string,string|int> $query
     * @return array<mixed>
     */
    public function get(string $url, array $query): array
    {
        $full = $url . (str_contains($url, '?') ? '&' : '?') . http_build_query($query);
        $req = $this->requests->createRequest('GET', $full);

        try {
            $res = $this->http->sendRequest($req);
        } catch (\Throwable $e) {
            throw new TransportException('HTTP request failed: ' . $e->getMessage(), 0, $e);
        }

        $code = $res->getStatusCode();

        if ($code < 200 || $code >= 300) {
            throw new TransportException("Unexpected HTTP status: {$code}");
        }

        $body = (string)$res->getBody();
        $data = json_decode($body, true);

        if (!is_array($data)) {
            throw new InvalidResponseException('Invalid JSON response');
        }

        return $data;
    }
}
