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
            $this->urlParser = new UrlParser(filter_input(INPUT_GET, 'url'));
            $this->parseUrl();
            $this->checkHttpMethod();
        } catch (RouteException $e) {
            $e->sendResponse();
        } catch (UnauthorizedCallException $e) {
            $e->sendResponse();
        }
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
     * @return void
     */
    private function parseUrl() {
        if (!is_null($this->urlParser) && $this->urlParser instanceof UrlParser) {
            $this->url = $this->urlParser->getUrl();
            $this->urlParser->searchForRoute($this->routes);
            $this->setHandlerParams($this->urlParser->getSplitedUrl());
        } else {
            throw new RouteException('Requested route is invalid');
        }
    }

    /**
     * Set handler params
     * @param mixed[] $splitedUrl
     * @return RequestHandler
     * @throws RouteException
     */
    private function setHandlerParams($splitedUrl) {
        if (is_array($splitedUrl) && count($splitedUrl) >= 2) {
            $this->type = $splitedUrl[0];
            $this->method = $splitedUrl[1];
            // get method params if exist
            for ($i = 2; $i < count($splitedUrl); $i++) {
                array_push($this->params, $splitedUrl[$i]);
            }
        } else {
            throw new RouteException('Requested route do not exist');
        }
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
