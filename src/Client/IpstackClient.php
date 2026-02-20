<?php

declare(strict_types=1);

namespace Ipstack\Client;

use Ipstack\Exception\ApiErrorException;
use Ipstack\Exception\IpstackException;
use Ipstack\Mapper\IpstackResultMapper;
use Ipstack\Model\IpstackResult;
use Ipstack\Transport\TransportInterface;

final class IpstackClient
{
    public function __construct(
        private readonly Config $config,
        private readonly Endpoint $endpoint,
        private readonly TransportInterface $transport,
        private readonly IpstackResultMapper $mapper
    ) {}

    public function lookup(string $ip, ?Options $options = null): IpstackResult
    {
        $data = $this->transport->get(
            $this->endpoint->standard($ip),
            $this->buildQuery($options)
        );
        $this->throwIfApiError($data);
        return $this->mapper->map($data);
    }

    public function lookupRequester(?Options $options = null): IpstackResult
    {
        $data = $this->transport->get(
            $this->endpoint->requester(),
            $this->buildQuery($options)
        );
        $this->throwIfApiError($data);
        return $this->mapper->map($data);
    }

    /** @param list<string> $ips */
    public function lookupBulk(array $ips, ?Options $options = null): array
    {
        if (count($ips) === 0) return [];
        if (count($ips) > 50) {
            // ipstack documents bulk constraints and related errors; we enforce early.
            throw new IpstackException('Bulk lookup supports up to 50 IPs per request.');
        }

        $data = $this->transport->get(
            $this->endpoint->bulk($ips),
            $this->buildQuery($options)
        );
        $this->throwIfApiError($data);

        // bulk typically returns a list of results
        if (!array_is_list($data)) {
            // sometimes APIs return object; be defensive
            return [$this->mapper->map($data)];
        }

        $out = [];
        foreach ($data as $row) {
            if (is_array($row)) {
                $this->throwIfApiError($row);
                $out[] = $this->mapper->map($row);
            }
        }
        return $out;
    }

    private function buildQuery(?Options $options): array
    {
        return array_merge(
            ['access_key' => $this->config->accessKey],
            $options?->toQuery() ?? []
        );
    }

    /** @param array<string,mixed> $data */
    private function throwIfApiError(array $data): void
    {
        if (!isset($data['error']) || !is_array($data['error'])) return;

        $code = $data['error']['code'] ?? null;
        $info = (string)($data['error']['info'] ?? 'Unknown API error');
        throw new ApiErrorException($code, $info);
    }
}
