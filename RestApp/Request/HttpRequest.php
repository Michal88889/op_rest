<?php

namespace RestApp\Request;

use RestApp\Response\HttpResponse;

/*
 * Base HTTP request class for handling incomming queries.
 * This class should be inherited by every request classes.
 */

abstract class HttpRequest {

    /**
     * Local response instnace
     * @var HttpResponse 
     */
    protected $response = null;

    /**
     * HTTP request constructor
     */
    public function __construct() {
        $this->response = new HttpResponse();
    }

    /**
     * Pack IP
     * @param string $ip
     * @return string
     */
    protected function packIp($ip) {
        return pack('N', ip2long($ip));
    }

    /**
     * Unpack IP
     * @param string $ip
     * @return string
     */
    protected function unpackIp($ip) {
        $unpacked = unpack('Nlong', $ip);
        if (isset($unpacked['long'])) {
            return long2ip($unpacked['long']);
        } else {
            return '';
        }
    }

}
