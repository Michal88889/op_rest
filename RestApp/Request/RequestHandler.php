<?php

namespace RestApp\Request;

use RestApp\Request\UrlParser;
//Exceptions
use RestApp\Exceptions\Request\RouteException;
use RestApp\Exceptions\Authorization\UnauthorizedCallException;

/*
 * Class responsible for handling incoming requests
 * Request validation and using proper http request controller
 */

class RequestHandler {

    /**
     * Incomming URL query
     * @var string 
     */
    private $url = '';

    /**
     * Request type with default value 'get'
     * @var string
     */
    private $type = 'get';

    /**
     * Request method name
     * @var string 
     */
    private $method = '';

    /**
     * Array of method params
     * @var mixed[] 
     */
    private $params = [];

    /**
     * Current request object
     * @var RestApp\Request\HttpRequest
     */
    private $currentRequest = null;

    /**
     * Url parser object
     * @var UrlParser 
     */
    private $urlParser = null;

    /**
     * Available fixed routes
     * @var mixed[]
     */
    private $routes = [
        'get/randomPost',
        'get/lastPosts/{0}'
    ];

    /**
     * Request classes list
     * Each Request class must inherit from HttpRequest class
     * @var mixed[] 
     */
    private $requests = [
        'get' => Get\GetRequest::class
    ];

    /**
     * Handle request by url structure
     * @param string $url
     */
    public function __construct() {
        try {
            $this->parseUrl()->checkHttpMethod();
        } catch (RouteException $e) {
            $e->sendResponse();
        } catch (UnauthorizedCallException $e) {
            $e->sendResponse();
        }
    }

    /**
     * Init url parser
     * @throws RouteException
     * @return UrlParser
     */
    private function getUrlParser() {
        if (is_null($this->urlParser) || !($this->urlParser instanceof UrlParser)) {
            try {
                $this->urlParser = new UrlParser(filter_input(INPUT_GET, 'url'));
            } catch (RouteException $e) {
                $e->sendResponse();
            }
        }
        return $this->urlParser;
    }

    /**
     * Check http method
     * @return boolean
     * @throws UnauthorizedCallException
     */
    private function checkHttpMethod() {
        $method = strtolower(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
        if ($method !== $this->type) {
            throw new UnauthorizedCallException('This operation is available only through http ' . $this->type . ' request.');
        }
        return true;
    }

    /**
     * Get route url
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Validate and parse requested URL
     * @return RequestHandler
     */
    private function parseUrl() {
        $this->url = $this->getUrlParser()->getUrl();
        $this->getUrlParser()->searchForRoute($this->routes);
        $this->setHandlerParams();
        return $this;
    }

    /**
     * Set handler params
     * @return RequestHandler
     */
    private function setHandlerParams() {
        $this->type = $this->getUrlParser()->getSplitedUrl(0);
        $this->method = $this->getUrlParser()->getSplitedUrl(1);
        // get method params if exist
        for ($i = 2; $i < count($this->getUrlParser()->getSplitedUrl()); $i++) {
            array_push($this->params, $this->getUrlParser()->getSplitedUrl($i));
        }
        return $this;
    }

    /**
     * Execute request accordingly to data received from URL
     * @throws RouteException
     */
    public function executeRequest() {
        if (isset($this->requests[$this->type]) && class_exists($this->requests[$this->type])) {
            //create request object
            $this->currentRequest = new $this->requests[$this->type];
            if (method_exists($this->currentRequest, $this->method)) {
                $valid = true;
                call_user_func_array([new $this->requests[$this->type], $this->method], $this->params);
            }
        }
        if (!$valid) {
            throw new RouteException('Requested route do not exist');
        }
    }

}
