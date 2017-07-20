<?php

namespace RestApp\Exceptions\Authorization;

use Exception;
use RestApp\Exceptions\GeneralException;

/*
 * Database exception used in RestApp\DataProviders\Database class.
 * This class handle database connection error.
 */

class EmptyCallException extends GeneralException {

    /**
     * Static message
     * @var string
     */
    protected $staticMessage = 'API call: ';

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        $this->useLog = false;
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

}
