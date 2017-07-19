<?php

namespace RestApp\Models;

use RestApp\App;

/*
 * Main model class for inheritance. Model is responsible for collecting data
 * from various sources.
 */

abstract class Model {

    /**
     * Database PDO instance
     * @var \PDO
     */
    protected $db = null;

    /**
     * Model constructor
     */
    public function __construct($options = ['db' => 1]) {
        //handle database object
        if (isset($options['db']) && $options['db']) {
            $this->db = App::getDatabase();
        }
    }

}
