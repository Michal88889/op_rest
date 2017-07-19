<?php

namespace RestApp\Request;

//Exceptions
use RestApp\Exceptions\Authorization\UnauthorizedCallException;

/*
 * Authorization class for handling request validation.
 * This class is responsible for secure and successful handshake with client.
 */

class Authorization {

    /**
     * Api key rceived from request
     * @var string 
     */
    private $apiKey = '';

    /**
     * Api token rceived from request
     * @var string 
     */
    private $apiToken = '';

    /**
     * HTTP authorization constructor
     */
    public function __construct() {
        
    }

    /**
     * Set requested api key
     * @param string $headerKey
     * @return Authorization
     * @throws UnauthorizedCallException
     */
    public function setApiKey($headerKey) {
        $this->apiKey = filter_input(INPUT_SERVER, $headerKey) ? filter_input(INPUT_SERVER, $headerKey) : filter_input(INPUT_SERVER, 'HTTP_' . $headerKey);

        if (!$this->apiKey || $this->apiKey === '') {
            throw new UnauthorizedCallException('Request authorization keys have not been sent');
        }
        return $this;
    }

    /**
     * Set requested api token
     * @param string $headerToken
     * @return Authorization
     * @throws UnauthorizedCallException
     */
    public function setApiToken($headerToken) {
        $this->apiToken = filter_input(INPUT_SERVER, $headerToken) ? filter_input(INPUT_SERVER, $headerToken) : filter_input(INPUT_SERVER, 'HTTP_' . $headerToken);

        if (!$this->apiToken || $this->apiToken === '') {
            throw new UnauthorizedCallException('Request authorization keys have not been sent');
        }
        return $this;
    }

    /**
     * Chekc if user is authorized to make request
     * @param mixed[] $storage
     * @return boolean
     */
    public function checkAuthorization($storage) {
        try {
            return $this->findAuthorizationRecord($storage);
        } catch (UnauthorizedCallException $e) {
            $e->sendResponse();
        }
    }

    /**
     * Find row in keys storage
     * @param mixed[] $storage
     * @return boolean
     * @throws UnauthorizedCallException
     */
    private function findAuthorizationRecord($storage) {
        $find = $this->apiKey . ':' . $this->apiToken;
        if (is_array($storage) && in_array($find, $storage)) {
            unset($storage);
            return true;
        } else {
            throw new UnauthorizedCallException('API key or token does not much');
        }
    }

}
