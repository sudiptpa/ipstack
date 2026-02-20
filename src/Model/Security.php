<?php

declare(strict_types=1);

namespace Ipstack\Model;

final class Security
{
    public function __construct(
        public readonly ?bool $isProxy = null,
        public readonly ?bool $isVpn = null,
        public readonly ?bool $isTor = null,

        // newer fields (May 2025 additions)
        public readonly ?string $proxyLastDetected = null,
        public readonly ?string $proxyLevel = null,
        public readonly ?string $vpnService = null,
        public readonly ?string $anonymizerStatus = null,
        public readonly ?string $hostingFacility = null,
    ) {}
}
