<?php

namespace RestApp\Response;

use RestApp\Response\ResponseBuilder;
//Exceptions
use RestApp\Exceptions\Response\ResponseParseException;

/*
 * Http response class responsible for creating proper response for client,
 * cookie management and validation.
 * Response is in JSON format and it is builded by ResponseBuilder class.
 */

class HttpResponse {

    /**
     * Local response builder
     * @var RestApp\Response\ResponseBuilder 
     */
    private $builder = null;

    /**
     * Handle response construction
     */
    public function __construct() {
        
    }

    /**
     * Init and return response builder
     * return RestApp\Response\ResponseBuilder
     */
    private function getBuilder() {
        if (is_null($this->builder)) {
            $this->builder = new ResponseBuilder();
        }

        return $this->builder;
    }

    /**
     * Set response fields
     * @param string|mixed[] $fields
     * @param mixed $value
     * @return RestApp\Response\HttpResponse $value
     */
    public function setResponse($fields, $value = '') {
        try {
            if (is_array($fields)) {
                foreach ($fields as $name => $fieldValue) {
                    $this->getBuilder()->setResponseBodyElement($name, $fieldValue);
                }
            } else {
                $this->getBuilder()->setResponseBodyElement($fields, $value);
            }
        } catch (ResponseParseException $e) {
            $e->sendResponse();
        }
        return $this;
    }

    /**
     * Send json response to client
     * @return mixed
     */
    public function sendResponse() {
        echo json_encode($this->getBuilder()->getResponseBody(), JSON_UNESCAPED_UNICODE);
    }

}
