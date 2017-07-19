<?php

namespace RestApp\Exceptions\Response;

use Exception;
use RestApp\Exceptions\GeneralException;

/*
 * Request route exception used in RestApp\Request\RequestHandler class.
 * This class handle invalid GET variable structure
 */

class ResponseParseException extends GeneralException {

    /**
     * Static message
     * @var string
     */
    protected $staticMessage = 'Response parse exception: ';

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

}
