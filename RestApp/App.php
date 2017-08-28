<?php

namespace RestApp;

use RestApp\DataProviders\Database;
use RestApp\DataProviders\FileManager;
use RestApp\Request\RequestHandler;
use RestApp\Request\UrlParser;
use RestApp\Request\Authorization;
//Exceptions
use Exception;
use RestApp\Exceptions\Database\DatabaseException;
use RestApp\Exceptions\Request\RouteException;
use RestApp\Exceptions\Authorization\EmptyCallException;
use RestApp\Exceptions\Authorization\UnauthorizedCallException;
use RestApp\Exceptions\Validation\ParamsException;
use RestApp\Exceptions\File\FileException;

/**
 * App engine of RestApp API
 * @author Michał Kuchmacz
 */
class App {

    /**
     * Singleton instance of App class
     * @var App 
     */
    protected static $_instance = null;

    /**
     * Is user authorized
     * @var boolean 
     */
    protected static $isAuthorized = false;

    /**
     * Static PDO connection
     * @var \PDO
     */
    protected static $db = null;

    /**
     * Static FileManager instance
     * @var FileManager 
     */
    protected static $fileManager = null;

    /**
     * Static request handler
     * @var RequestHandler
     */
    protected static $requestHandler = null;

    /**
     * Config file array
     * @var mixed[] 
     */
    private static $config = [];

    /**
     * Route URL
     * @var string 
     */
    public static $url = '';

    /**
     * Start microtime
     * @var int 
     */
    private static $startTime = 0;

    /**
     * Start RestAPI
     */
    public static function start() {
        if (!isset(self::$_instance)) {
            self::$_instance = new App();
        }
        return self::$_instance;
    }

    /**
     * Hide constructor, protected so only subclasses and self can use
      @throws DatabaseException
     */
    protected function __construct() {
        //*** Init start time ***//
        self::$startTime = microtime(true);

        //*** Client request authorization ***//
        self::authorizeClient();

        //*** Execute request ***//
        self::handleRequest();
    }

    /**
     * Load config file to variable
     * @return mixed[]
     */
    private static function loadConfig() {
        if (empty(self::$config)) {
            self::$config = include 'config.php';
        }
        return self::$config;
    }

    /**
     * Get config param where '.' is array dimension separator
     * For example: 'db.type' will return array element ['db']['type']
     * @param string $name
     * @return string mixed[] null
     */
    public static function getConfig($name) {
        $search = self::loadConfig();
        //search in array
        if (strpos($name, '.') > 0) {
            $array = explode('.', $name);
            $result = '';
            foreach ($array as $value) {
                if (isset($search[$value])) {
                    $result = $search[$value];
                    $search = $search[$value];
                }
            }
        } else { //get directly
            $result = $search[$name];
        }

        return $result;
    }

    /**
     * Init request handler of app
     * @return RequestHandler
     */
    public static function getRequestHandler() {
        if (is_null(self::$requestHandler) || !(self::$requestHandler instanceof RequestHandler)) {
            self::$requestHandler = new RequestHandler();
            self::$requestHandler->setUrlParser(new UrlParser(filter_input(INPUT_GET, 'url')));
            self::$url = self::$requestHandler->getUrlParser()->getUrl();
        }
        return self::$requestHandler;
    }

    /**
     * Authorize client
     * @return boolean
     * @throws UnauthorizedCallException, EmptyCallException
     */
    private static function authorizeClient() {
        try {
            $auth = new Authorization();
            //get api key and token
            $auth->setApiKey(self::getConfig('header_keys.key'));
            $auth->setApiToken(self::getConfig('header_keys.token'));
            //check authorization
            self::$isAuthorized = $auth->checkAuthorization(self::getFileManager()->getStorage('api_keys'));
        } catch (EmptyCallException $e) {
            //Special exception which return string message to client instead of json array
            $e->sendMessage();
        } catch (UnauthorizedCallException $e) {
            $e->sendResponse();
        }
        return self::$isAuthorized;
    }

    /**
     * Init file manager of app
     * @return FileManager
     */
    public static function getFileManager() {
        if (is_null(self::$fileManager) || !(self::$fileManager instanceof FileManager)) {
            try {
                self::$fileManager = new FileManager(__DIR__, self::getConfig('storage'));
            } catch (FileException $e) {
                $e->sendResponse();
            }
        }
        return self::$fileManager;
    }

    /**
     * Init or receive database
     * @return \PDO
     */
    public static function getDatabase() {
        try {
            if (is_null(self::$db) || !(self::$db instanceof \PDO)) {
                $dbConfig = self::getConfig('db');
                self::$db = Database::instance()->getConnection($dbConfig['type'], $dbConfig['host'], $dbConfig['name'], $dbConfig['user'], $dbConfig['pass']);
            }
        } catch (DatabaseException $e) {
            $e->sendResponse();
        } catch (Exception $e) {
            DatabaseException::capture($e)->sendResponse();
        }
        return self::$db;
    }

    /**
     * Handle Request from given URL address
     * and return response json array to client
     */
    public static function handleRequest() {
        try {
            self::getRequestHandler()->executeRequest();
        } catch (RouteException $e) {
            $e->sendResponse();
        } catch (UnauthorizedCallException $e) {
            $e->sendResponse();
        } catch (ParamsException $e) {
            $e->sendResponse();
        } catch (\PDOException $e) {
            DatabaseException::capture($e)->sendResponse();
        } catch (Exception $e) {
            RouteException::capture($e)->sendResponse();
        }
    }

    /**
     * Get script execution time
     * @return float
     */
    public static function getExecutionTime() {
        return number_format((microtime(true) - self::$startTime) / 60, 6, '.', '');
    }

}
