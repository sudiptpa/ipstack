<?php

declare(strict_types=1);

namespace Ipstack\Client;

final class Endpoint
{
    public function __construct(
        private readonly string $baseUrl = 'https://api.ipstack.com'
    ) {}

    public function standard(string $ip): string
    {
        return rtrim($this->baseUrl, '/') . '/' . rawurlencode($ip);
    }

    /** @param list<string> $ips */
    public function bulk(array $ips): string
    {
        return rtrim($this->baseUrl, '/') . '/' . implode(',', array_map('rawurlencode', $ips));
    }

    public function requester(): string
    {
        return rtrim($this->baseUrl, '/') . '/check';
    }
}
