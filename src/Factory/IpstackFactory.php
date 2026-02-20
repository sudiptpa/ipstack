<?php

declare(strict_types=1);

namespace Ipstack\Factory;

use Ipstack\Client\Config;
use Ipstack\Client\Endpoint;
use Ipstack\Client\IpstackClient;
use Ipstack\Mapper\IpstackResultMapper;
use Ipstack\Transport\TransportInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

use Ipstack\Transport\Psr18Transport;



final class IpstackFactory
{
    private ?string $accessKey = null;
    private string $baseUrl = 'https://api.ipstack.com';

    private ?TransportInterface $transport = null;

    public function withAccessKey(#[\SensitiveParameter] string $key): self
    {
        $this->accessKey = $key;

        return $this;
    }

    public function withBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function withTransport(TransportInterface $transport): self
    {
        $this->transport = $transport;

        return $this;
    }

    public function withPsr18(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory
    ): self {
        $this->transport = new Psr18Transport($client, $requestFactory);

        return $this;
    }

    /**
     * If you prefer PSR-18 wiring here, pass an adapter transport you create externally.
     * (This factory stays PSR-only and dependency-light.)
     */
    public function build(): IpstackClient
    {
        if ($this->accessKey === null || $this->accessKey === '') {
            throw new \InvalidArgumentException('Access key is required.');
        }

        $config = new Config($this->accessKey, $this->baseUrl);
        $endpoint = new Endpoint($this->baseUrl);
        $mapper = new IpstackResultMapper();

        if ($this->transport === null) {
            throw new \InvalidArgumentException(
                'No transport configured. Provide a PSR-18 based transport via withTransport().'
            );
        }

        return new IpstackClient($config, $endpoint, $this->transport, $mapper);
    }
}
