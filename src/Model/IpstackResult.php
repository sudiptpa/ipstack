<?php

declare(strict_types=1);

namespace Ipstack\Model;

use Ipstack\Model\Attributes\MapFrom;


final class IpstackResult implements \JsonSerializable
{
    public function __construct(
        #[MapFrom('ip')] public readonly string $ip,
        #[MapFrom('type')] public readonly ?string $type = null,
        #[MapFrom('city')] public readonly ?string $city = null,
        #[MapFrom('zip')] public readonly ?string $zip = null,
        #[MapFrom('latitude')] public readonly ?float $latitude = null,
        #[MapFrom('longitude')] public readonly ?float $longitude = null,

        public readonly Country $country = new Country(),
        public readonly Region $region = new Region(),
        public readonly ?Location $location = null,
        public readonly ?Connection $connection = null,
        public readonly ?Security $security = null,
        public readonly ?Routing $routing = null,

        /** @var array<string,mixed> */
        private readonly array $raw = [],
    ) {}

    public function formatted(): string
    {
        $parts = array_filter([$this->city, $this->zip, $this->region->name, $this->country->name]);
        return implode(', ', $parts);
    }

    /** @return array<string,mixed> */
    public function raw(): array
    {
        return $this->raw;
    }

    public function jsonSerialize(): array
    {
        return $this->raw;
    }
}
