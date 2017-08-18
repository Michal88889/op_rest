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
        //get
        'get/randomPost',
        'get/lastPosts/:num',
        'get/getOnlineUsers',
        //post
        'post/addPost',
        //put
        'put/editPost/:id',
        'put/editUser/:id',
        'put/banUser/:id',
        //delete
        'delete/removePost/:id',
    ];

    /**
     * Request classes list
     * Each Request class must inherit from HttpRequest class
     * @var mixed[] 
     */
    private $requests = [
        'get' => Get\GetRequest::class,
        'post' => Post\PostRequest::class,
        'put' => Put\PutRequest::class,
        'delete' => Delete\DeleteRequest::class,
    ];

    /**
     * Get url parser
     * @throws RouteException
     * @return UrlParser
     */
    public function getUrlParser() {
        return $this->urlParser;
    }

    /**
     * Set url parser
     * @param UrlParser $parser
     * @return void
     */
    public function setUrlParser(UrlParser $parser) {
        if (!is_null($parser) && ($parser instanceof UrlParser)) {
            $this->urlParser = $parser;
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
     * Validate and parse requested URL
     * @return RequestHandler
     */
    private function parseUrl() {
        $this->getUrlParser()->searchForRoute($this->routes);
        return $this;
    }

    /**
     * Set handler params:
     * type = request type (get/post/put/delete)
     * method = method name
     * params = method parameters
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
        $this->parseUrl()->$this->setHandlerParams();
        //check if parse is valid
        if ($this->checkHttpMethod() && isset($this->requests[$this->type]) && class_exists($this->requests[$this->type])) {
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
