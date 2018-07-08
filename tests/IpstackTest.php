<?php

namespace Sujip\Ipstack\Test;

use PHPUnit\Framework\TestCase;
use Sujip\Ipstack\Exception\Forbidden;
use Sujip\Ipstack\Http\Response;
use Sujip\Ipstack\Ipstack;

/**
 * Class IpstackTest.
 */
class IpstackTest extends TestCase
{
    /**
     * @var mixed
     */
    private $payload;

    public function setUp()
    {
        $this->payload = file_get_contents(__DIR__ . '/Mock/Response/response.json');
    }

    public function tearDown()
    {
        $this->payload = null;
    }

    public function testShouldThrowExceptionForEmptyIpHost()
    {
        $this->expectException(Forbidden::class);
        $this->expectExceptionMessage('Error: No IP specified');
        $this->expectExceptionCode(403);

        $geo = (new Ipstack(''))->call();
    }

    public function testShouldReturnValidResponse()
    {
        $body = $this->payload;

        $result = (new Response($body))->getBody();

        $body = json_decode($body, true);

        $this->assertEquals($body, $result);
    }
}
