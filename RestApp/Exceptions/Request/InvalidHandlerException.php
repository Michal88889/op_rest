<?php

namespace RestApp\Exceptions\Request;

use Exception;
use RestApp\Exceptions\GeneralException;

/*
 * Request invalid request handler exception used in RestApp\Request\RequestHandler class.
 * This class handle invalid RequestHandler object exception
 */

class InvalidHandlerException extends GeneralException {

    /**
     * Static message
     * @var string
     */
    protected $staticMessage = 'Invalid RequestHandler: ';

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

}
