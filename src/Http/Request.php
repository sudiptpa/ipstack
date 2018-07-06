<?php

namespace Sujip\Ipstack\Http;

use Exception;
use Sujip\Ipstack\Exception\Forbidden;

/**
 * @author Sujip Thapa <support@sujipthapa.co>
 */
class Request
{
    /**
     * @var string
     */
    protected $ip;

    /**
     * @param $ip
     */
    public function __construct($ip = null)
    {
        $this->ip = $ip;
    }

    /**
     * @return null
     */
    public function make()
    {
        if (empty($this->ip)) {
            $this->throwException('Error: No IP specified', 403);
        }

        try {
            $response = file_get_contents(
                sprintf('https://ipstack.com/ipstack_api.php?ip=%s', $this->ip)
            );

        } catch (Exception $e) {
            $this->throwException('Forbidden', 403);
        }

        return new Response($response);
    }

    /**
     * @param $message
     * @param $code
     */
    public function throwException($message, $code = 400)
    {
        throw new Forbidden($message, $code);
    }
}
