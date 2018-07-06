<?php

namespace Sujip\Ipstack\Http;

/**
 * Class Response.
 */
class Response
{
    /**
     * @var mixed
     */
    protected $response;

    /**
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return json_decode($this->response, true);
    }
}
