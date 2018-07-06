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
     * @var string
     */
    protected $api_key;

    /**
     * Create new instance.
     *
     * @param $ip
     * @param null $api_key
     */
    public function __construct($ip = null, $api_key = null)
    {
        $this->ip = $ip;
        $this->api_key = $api_key;
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
            $url = sprintf('https://ipstack.com/ipstack_api.php?ip=%s', $this->ip);

            if ($this->api_key) {
                $url = sprintf('http://api.ipstack.com/%s?access_key=%s', $this->ip, $this->api_key);
            }

            $response = file_get_contents($url);
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
