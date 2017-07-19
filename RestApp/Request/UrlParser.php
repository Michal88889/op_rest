<?php

namespace RestApp\Request;

//Exceptions
use RestApp\Exceptions\Request\RouteException;

/*
 * Base HTTP request class for handling incomming queries.
 * This class should be inherited by every request classes.
 */

class UrlParser {

    /**
     * URL request parser
     * @var string 
     */
    protected $url = 'null';

    /**
     * Splited URL
     * @var mixed[] 
     */
    protected $splitedUrl = [];

    /**
     * HTTP request constructor
     */
    public function __construct($url) {
        $this->setUrl($url);
        $this->splitUrl();
    }

    /**
     * Set incomming URL query
     * @param string $url
     * @throws RouteException
     */
    private function setUrl($url) {
        if (is_string($url) && (string) $url !== '') {
            $this->url = substr($url, strlen($url) - 1, 1) === '/' ? substr($url, 0, strlen($url) - 1) : $url;
        } else {
            throw new RouteException('Requested route is empty');
        }
    }

    /**
     * Get URL
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Get splited URL array
     * @return mixed[]
     */
    public function getSplitedUrl() {
        if (empty($this->splitedUrl)) {
            return $this->splitUrl();
        } else {
            return $this->splitedUrl;
        }
    }

    /**
     * Split url
     * @param string|null
     * @return mixed[]
     * @throws RouteException
     */
    private function splitUrl() {
        if (strpos($this->url, '/') > 0) {
            $this->splitedUrl = explode('/', $this->url);
            return $this->splitedUrl;
        } else {
            throw new RouteException('Requested route is invalid');
        }
    }

    /**
     * Search for valid request
     * @param mixed[] $routes
     * @return mixed[]
     * @throws RouteException
     */
    public function searchForRoute($routes) {
        $index = -1;
        foreach ($routes as $routeIndex => $route) {
            $splitedRoute = strpos($route, '/') > 0 ? explode('/', $route) : [];
            if (count($splitedRoute) !== count($this->splitedUrl)) {
                continue;
            }
            foreach ($splitedRoute as $elementIndex => $routeElement) {
                if (!$this->isParam($routeElement) && (!isset($this->splitedUrl[$elementIndex]) || (string) $this->splitedUrl[$elementIndex] !== $routeElement)) {
                    break;
                }
                $index = $routeIndex;
            }
        }

        if ($index > -1) {
            return $routes[$index];
        } else {
            throw new RouteException('Requested route do not exist');
        }
    }

    /**
     * Check if string is param
     * @param string $string
     * @return boolean
     */
    private function isParam($string) {
        return strpos($string, '{') === 0 && strpos($string, '}') === (strlen($string) - 1);
    }

}
