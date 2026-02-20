<?php

declare(strict_types=1);

namespace Ipstack\Model;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/** @implements IteratorAggregate<int, IpstackResult> */
final class IpstackCollection implements IteratorAggregate, Countable
{
    /** @param list<IpstackResult> $items */
    public function __construct(private readonly array $items) {}

    /**
     * @return ArrayIterator<int, IpstackResult>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /** @return list<IpstackResult> */
    public function all(): array
    {
        return $this->items;
    }
}
