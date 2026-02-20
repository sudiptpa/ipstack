<?php

declare(strict_types=1);

namespace Ipstack\Model;

use Ipstack\Model\Attributes\MapFrom;

final class Region
{
    public function __construct(
        #[MapFrom('region_code')] public readonly ?string $code = null,
        #[MapFrom('region_name')] public readonly ?string $name = null,
    ) {}
}
