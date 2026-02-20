<?php

declare(strict_types=1);

namespace Ipstack\Model;

final class Routing
{
    public function __construct(
        public readonly ?string $msa = null,
        public readonly ?string $dma = null,
        public readonly ?float $radius = null,
        public readonly ?string $ipRoutingTyp = null,
        public readonly ?string $connectionType = null,
    ) {}
}
