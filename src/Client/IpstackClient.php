<?php

declare(strict_types=1);

namespace Ipstack\Client;

use Ipstack\Exception\ApiErrorException;
use Ipstack\Exception\BatchNotSupportedException;
use Ipstack\Exception\InvalidFieldsException;
use Ipstack\Exception\IpstackException;
use Ipstack\Exception\RateLimitException;
use Ipstack\Exception\TooManyIpsException;
use Ipstack\Mapper\IpstackResultMapper;
use Ipstack\Model\IpstackCollection;
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

    /**
     * @param list<string> $ips
     */
    public function lookupBulk(array $ips, ?Options $options = null): IpstackCollection
    {
        if ($ips === []) {
            return new IpstackCollection([]);
        }

        if (count($ips) > 50) {
            throw new IpstackException('Bulk lookup supports up to 50 IPs per request.');
        }

        /** @var array<mixed> $data */
        $data = $this->transport->get(
            $this->endpoint->bulk($ips),
            $this->buildQuery($options)
        );

        // If API error wrapper returned (assoc), throw
        if (!array_is_list($data)) {
            /** @var array<string,mixed> $assoc */
            $assoc = $data;
            $this->throwIfApiError($assoc);
            return new IpstackCollection([$this->mapper->map($assoc)]);
        }

        $out = [];
        foreach ($data as $row) {
            if (!is_array($row)) {
                continue;
            }

            /** @var array<string,mixed> $rowAssoc */
            $rowAssoc = $row;

            $this->throwIfApiError($rowAssoc);
            $out[] = $this->mapper->map($rowAssoc);
        }

        return new IpstackCollection($out);
    }

    /**
     * @return array<string, string|int>
     */
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

        // Map known ipstack error codes to domain exceptions
        switch ((int)$code) {
            case 104:
                throw new RateLimitException($code, $info);
            case 301:
                throw new InvalidFieldsException($code, $info);
            case 302:
                throw new TooManyIpsException($code, $info);
            case 303:
                throw new BatchNotSupportedException($code, $info);
            default:
                throw new ApiErrorException($code, $info);
        }
    }
}
