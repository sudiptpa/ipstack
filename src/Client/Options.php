<?php

declare(strict_types=1);

namespace Ipstack\Client;

final class Options
{
    /** @var array<string,string|int> */
    private array $query = [];

    public static function create(): self
    {
        return new self();
    }

    /** @param list<string>|string $fields */
    public function fields(array|string $fields): self
    {
        $this->query['fields'] = is_array($fields) ? implode(',', $fields) : $fields;
        return $this;
    }

    public function language(string $lang): self
    {
        $this->query['language'] = $lang;
        return $this;
    }

    public function security(bool $enabled = true): self
    {
        $this->query['security'] = $enabled ? 1 : 0;
        return $this;
    }

    public function hostname(bool $enabled = true): self
    {
        $this->query['hostname'] = $enabled ? 1 : 0;
        return $this;
    }

    /** @return array<string,string|int> */
    public function toQuery(): array
    {
        return $this->query;
    }
}
