<?php

declare(strict_types=1);

namespace Ipstack\Tests\Fakes;

use Ipstack\Transport\TransportInterface;

final class FakeTransport implements TransportInterface
{
    /** @var array<string,mixed> */
    private array $next;

    /** @param array<string,mixed> $next */
    public function __construct(array $next)
    {
        $this->next = $next;
    }

    /**
     * @param array<string,string|int> $query
     * @return array<mixed>
     */
    public function get(string $url, array $query): array
    {
        return $this->next;
    }
}
