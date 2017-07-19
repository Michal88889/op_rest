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

}
