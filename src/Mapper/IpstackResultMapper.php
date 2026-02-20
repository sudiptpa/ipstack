<?php

declare(strict_types=1);

namespace Ipstack\Mapper;

use Ipstack\Model\Attributes\MapFrom;
use Ipstack\Model\{
    Country,
    Region,
    Location,
    Connection,
    Security,
    Routing,
    IpstackResult
};
use ReflectionClass;

final class IpstackResultMapper
{
    /** @param array<string,mixed> $data */
    public function map(array $data): IpstackResult
    {
        /** @var Country $country */
        $country = $this->hydrate(Country::class, $data);

        /** @var Region $region */
        $region = $this->hydrate(Region::class, $data);

        $location = null;
        if (isset($data['location']) && is_array($data['location'])) {
            $location = new Location(
                capital: $data['location']['capital'] ?? null,
                languages: is_array($data['location']['languages'] ?? null) ? $data['location']['languages'] : [],
            );
        }

        $connection = null;
        if (isset($data['connection']) && is_array($data['connection'])) {
            $c = $data['connection'];
            $connection = new Connection(
                asn: isset($c['asn']) ? (int)$c['asn'] : null,
                isp: $c['isp'] ?? null,
                sld: $c['sld'] ?? null,
                tld: $c['tld'] ?? null,
                carrier: $c['carrier'] ?? null,
                home: isset($c['home']) ? (bool)$c['home'] : null,
                organizationType: $c['organization_type'] ?? null,
                isicCode: $c['isic_code'] ?? null,
                naicsCode: $c['naics_code'] ?? null,
            );
        }

        $security = null;
        if (isset($data['security']) && is_array($data['security'])) {
            $s = $data['security'];
            $security = new Security(
                isProxy: isset($s['is_proxy']) ? (bool)$s['is_proxy'] : null,
                isVpn: isset($s['is_vpn']) ? (bool)$s['is_vpn'] : null,
                isTor: isset($s['is_tor']) ? (bool)$s['is_tor'] : null,
                proxyLastDetected: $s['proxy_last_detected'] ?? null,
                proxyLevel: $s['proxy_level'] ?? null,
                vpnService: $s['vpn_service'] ?? null,
                anonymizerStatus: $s['anonymizer_status'] ?? null,
                hostingFacility: $s['hosting_facility'] ?? null,
            );
        }

        // Routing fields are top-level in the response (per changelog additions)
        $routing = new Routing(
            msa: $data['msa'] ?? null,
            dma: $data['dma'] ?? null,
            radius: isset($data['radius']) ? (float)$data['radius'] : null,
            ipRoutingTyp: $data['ip_routing_typ'] ?? null,
            connectionType: $data['connection_type'] ?? null,
        );

        return new IpstackResult(
            ip: (string)($data['ip'] ?? ''),
            type: $data['type'] ?? null,
            city: $data['city'] ?? null,
            zip: $data['zip'] ?? null,
            latitude: isset($data['latitude']) ? (float)$data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float)$data['longitude'] : null,

            country: $country,
            region: $region,
            location: $location,
            connection: $connection,
            security: $security,
            routing: $routing,

            raw: $data
        );
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string,mixed> $data
     * @return T
     */
    private function hydrate(string $class, array $data): object
    {
        $rc = new ReflectionClass($class);
        $args = [];

        foreach ($rc->getConstructor()?->getParameters() ?? [] as $p) {
            $name = $p->getName();
            $prop = $rc->hasProperty($name) ? $rc->getProperty($name) : null;

            $key = $name;
            if ($prop) {
                $attrs = $prop->getAttributes(MapFrom::class);
                if ($attrs) {
                    $key = $attrs[0]->newInstance()->key;
                }
            }

            $args[$name] = $data[$key] ?? $p->getDefaultValue();
        }

        return $rc->newInstanceArgs($args);
    }
}
