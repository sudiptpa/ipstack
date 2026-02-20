<?php

declare(strict_types=1);

namespace Ipstack\Transport;

interface TransportInterface
{
    /** @return array<string,mixed> */
    public function get(string $url, array $query): array;
}
