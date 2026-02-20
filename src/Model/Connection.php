<?php

declare(strict_types=1);

namespace Ipstack\Model;

final class Connection
{
    public function __construct(
        public readonly ?int $asn = null,
        public readonly ?string $isp = null,

        // newer fields (May 2025 additions)
        public readonly ?string $sld = null,
        public readonly ?string $tld = null,
        public readonly ?string $carrier = null,
        public readonly ?bool $home = null,
        public readonly ?string $organizationType = null,
        public readonly ?string $isicCode = null,
        public readonly ?string $naicsCode = null,
    ) {}
}
