<?php

declare(strict_types=1);

namespace Ipstack\Model\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class MapFrom
{
    public function __construct(public readonly string $key) {}
}
