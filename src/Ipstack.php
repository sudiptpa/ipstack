<?php

namespace Sujip\Ipstack;

use Sujip\Ipstack\Exception\Forbidden;
use Sujip\Ipstack\Http\Request;

/**
 * Class GeoIp.
 */
class Ipstack
{
    /**
     * @var mixed
     */
    protected $items = [];

    /**
     * Create new instance.
     *
     * @param $ip
     * @param $api_key
     */
    public function __construct($ip, $api_key = null)
    {
        $this->items = $this->call($ip, $api_key);
    }

    /**
     * Make an API call with IP
     *
     * @param $ip
     * @param $api_key
     *
     * @return \Sujip\Ipstack\Http\Response
     */
    public function call($ip, $api_key)
    {
        try {
            $response = (new Request($ip, $api_key))->make();
        } catch (Forbidden $e) {
            throw new Forbidden('Error: No IP specified', 403);
        }

        return $response->getBody();
    }

    /**
     * Get IP address, eg: 27.34.19.106
     *
     * @return string
     */
    public function ip()
    {
        return $this->resolve('ip');
    }

    /**
     * Get continent eg: Asia
     *
     * @return string
     */
    public function continent()
    {
        return $this->resolve('continent_name');
    }

    /**
     * Get country code, eg:NP
     *
     * @return string
     */
    public function countryCode()
    {
        return $this->resolve('country_code');
    }

    /**
     * Get country name, eg: Nepal
     *
     * @return string
     */
    public function country()
    {
        return $this->resolve('country_name');
    }

    /**
     * Resolve if the API returns the coulumn.
     *
     * @param $key
     * @param $default
     */
    public function resolve($key, $default = null)
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        return $default;
    }

    /**
     * Get your region eg: Central Region
     *
     * @return string
     */
    public function region()
    {
        return $this->resolve('region_name');
    }

    /**
     * Get city, eg: Kathmandu
     *
     * @return string
     */
    public function city()
    {
        return $this->resolve('city');
    }

    /**
     * Get formatted address by IP, eg: Kathmandu, Central Region, Nepal
     *
     * @return string
     */
    public function formatted()
    {
        $address = array_filter([
            $this->city(),
            $this->zip(),
            $this->region(),
            $this->country(),
        ]);

        return implode(', ', $address);
    }

    /**
     * Get zip code, eg: 33700
     *
     * @return string
     */
    public function zip()
    {
        return $this->resolve('zip');
    }

    /**
     * Get latitude eg: 27.6667
     *
     * @return string
     */
    public function latitude()
    {
        return $this->resolve('latitude');
    }

    /**
     * Get longitude eg: 85.3167
     *
     * @return string
     */
    public function longitude()
    {
        return $this->resolve('longitude');
    }

    /**
     * Get timezone eg: Asia/Kathmandu
     *
     * @return string
     */
    public function timezone()
    {
        return $this->resolve('time_zone')['id'] ?? null;
    }

    /**
     * Get connection, isp eg: WorldLink Communications Pvt Ltd
     *
     * @return string
     */
    public function isp()
    {
        return $this->resolve('connection')['isp'] ?? null;
    }

    /**
     * Get curreny code eg: NPR
     *
     * @return string
     */
    public function currency()
    {
        return $this->resolve('currency')['code'] ?? null;
    }
}
