<?php

declare(strict_types=1);

namespace Ipstack\Transport;

interface TransportInterface
{
    /**
     * @param array<string,string|int> $query
     * @return array<mixed>
     */
    public function get(string $url, array $query): array;
}
