<?php

declare(strict_types=1);

namespace Ipstack\Client;

final class Config
{
    public function __construct(
        #[\SensitiveParameter]
        public readonly string $accessKey,
        public readonly string $baseUrl = 'https://api.ipstack.com'
    ) {}
}
