<?php

namespace RestApp\Exceptions\Database;

use Exception;
use RestApp\Exceptions\GeneralException;

/*
 * Database exception used in RestApp\DataProviders\Database class.
 * This class handle database connection error.
 */

class DatabaseException extends GeneralException {

    /**
     * Static message
     * @var string
     */
    protected $staticMessage = 'Internal database connection error: ';

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

}
