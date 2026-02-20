<?php

declare(strict_types=1);

namespace Ipstack;

use Ipstack\Factory\IpstackFactory;

final class Ipstack
{
    public static function factory(): IpstackFactory
    {
        return new IpstackFactory();
    }
}
