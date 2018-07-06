<?php

namespace Sujip\Ipstack\Exception;

use Exception;

/**
 * Class Forbidden.
 */
class Forbidden extends Exception
{
    /**
     * @param $message
     * @param $code
     */
    public function __construct($message, $code = 400)
    {
        parent::__construct($message, $code);
    }
}
