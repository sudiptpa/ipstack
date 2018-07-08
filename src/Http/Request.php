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
    protected $param = [];

    /**
     * Create new instance.
     *
     * @param $param
     */
    public function __construct($param)
    {
        $this->param = $param;
    }

    /**
     * @return mixed
     */
    public function getEndPoint()
    {
        $url = sprintf('https://ipstack.com/ipstack_api.php?ip=%s', $this->param['ip']);

        if ($this->param['api_key']) {
            $protocol = $this->param['secure'] ? 'https' : 'http';

            $url = sprintf(
                '%s://api.ipstack.com/%s?access_key=%s',
                $protocol,
                $this->param['ip'],
                $this->param['api_key']
            );
        }

        return $url;
    }

    /**
     * @return \Sujip\Ipstack\Http\Response
     */
    public function make()
    {
        if (empty($this->param['ip'])) {
            $this->throwException('Error: No IP specified', 403);
        }

        try {
            $response = file_get_contents($this->getEndPoint());
        } catch (Exception $e) {
            $this->throwException('Forbidden', 403);
        }

        return new Response($response);
    }

    /**
     * @param $message
     * @param $code
     *
     * @return \Sujip\Ipstack\Exception\Forbidden
     */
    public function throwException($message, $code = 400)
    {
        throw new Forbidden($message, $code);
    }
}
