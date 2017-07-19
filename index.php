<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/* * ***************************************************
 * This application is responsible for sharing server content
 * with autohorized clients.
 * 
 * @author Michał Kuchmacz
 * ****************************************************
 */

require_once 'autoload.php';

use RestApp\App;

/*
 * Bootsrap file for RestApp API
 * Autoload of neccessery classes
 * Handle procces in protected constructor
 * App singleton class is constructed here
 */
$application = App::start();
