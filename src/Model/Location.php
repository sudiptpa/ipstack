<?php

declare(strict_types=1);

namespace Ipstack\Model;

final class Location
{
    /**
     * @param list<array{name?:string,code?:string,native?:string}> $languages
     */
    public function __construct(
        public readonly ?string $capital = null,
        public readonly array $languages = [],
    ) {}
}
