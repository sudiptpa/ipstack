<?php

declare(strict_types=1);

namespace Ipstack\Model;

use Ipstack\Model\Attributes\MapFrom;

final class Country
{
    public function __construct(
        #[MapFrom('country_code')] public readonly ?string $code = null,
        #[MapFrom('country_name')] public readonly ?string $name = null,
        #[MapFrom('country_flag')] public readonly ?string $flag = null,
        #[MapFrom('calling_code')] public readonly ?string $callingCode = null,
    ) {}
}
