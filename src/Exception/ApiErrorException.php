<?php

declare(strict_types=1);

namespace Ipstack\Exception;

class ApiErrorException extends IpstackException
{
    public function __construct(
        public readonly int|string|null $apiCode,
        string $message
    ) {
        parent::__construct($message);
    }
}
