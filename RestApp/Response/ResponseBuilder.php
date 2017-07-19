<?php

namespace RestApp\Response;

use RestApp\App;
//Exceptions
use RestApp\Exceptions\Response\ResponseParseException;

/*
 * Http response builder class responsible for building response JSON.
 * This class only creates json array, it does not send anything to client.
 */

class ResponseBuilder {

    /**
     * Response body structure
     * @var mixed[]
     */
    protected $responseBody = [
        'version' => '',
        'date_time' => '',
        'execution_time' => 0,
        'route' => '',
        'status' => 1,
        'message' => '',
        'error' => [
            'message' => '',
            'exception' => ''
        ],
        'result' => []
    ];

    /**
     * Handle response construction
     */
    public function __construct() {
        try {
            $this->setResponseBodyElement('version', App::getConfig('version'));
            $this->setResponseBodyElement('date_time', date('Y-m-d H:i:s'));
            $this->setResponseBodyElement('route', str_replace('/', '->', App::$url));
            $this->setResponseBodyElement('execution_time', App::getExecutionTime());
        } catch (ResponseParseException $e) {
            $e->sendResponse();
        }
    }

    /**
     * Set response body element
     * @param string $key
     * @param mixed $value
     * @return mixed[] response body
     */
    public function setResponseBodyElement($key, $value) {
        if (strpos($key, '.') > 0) {
            $split = explode(".", $key);
            $this->setArrayValue($this->responseBody, $split, $value);
        } else {
            if (is_string($key)) {
                $this->responseBody[$key] = $value;
            } else {
                throw new ResponseParseException('Invalid response element during response build procces');
            }
        }
        return $this->responseBody;
    }

    /**
     * Get response body
     * @return mixed[]
     */
    public function getResponseBody() {
        return $this->responseBody;
    }

    /**
     * Set multidimensional array value
     * @param mixed[] $data
     * @param mixed $split
     * @param mixed $value
     * @param int $level
     */
    protected function setArrayValue(&$data, $split, $value, $level = 0) {
        $level++;
        foreach ($data as $name => &$val) {
            if (is_array($val)) {
                $this->setArrayValue($val, $split, $value, $level);
            } elseif ((string) $name === (string) end($split) && count($split) === $level) {
                $data[$name] = $value;
            }
        }
    }

}
