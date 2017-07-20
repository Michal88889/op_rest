<?php

namespace RestApp\Exceptions;

use Exception;
use RestApp\App;
use RestApp\Response\HttpResponse;

/*
 * General exception class for error handling.
 * This class should be inherited to more detailed exception classess.
 * To handle exception properly it is required to return response in every catch section.
 */

abstract class GeneralException extends Exception {

    /**
     * Static message
     * @var string
     */
    protected $staticMessage = 'Following error occurred: ';

    /**
     * Use log file for exceptions
     * @var boolean 
     */
    protected $useLog = true;

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    /**
     * Destruct function execute exit to stop code after exception handle
     */
    public function __destruct() {
        exit;
    }

    /**
     * Catch exception dynamically when it is required to send error response
     * within external exception classs. For example in base Exception class.
     * @param Exception $exception
     * @return GeneralException
     */
    public static function capture($exception) {
        $obj = new self($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        return $obj;
    }

    /**
     * Send string message to client
     * @return void
     */
    public function sendMessage() {
        $this->logException();
        echo $this->composeMessage();
    }

    /**
     * Send response to client
     * @return void
     */
    public function sendResponse() {
        $response = new HttpResponse();
        $this->logException();
        $response->setResponse([
            'status' => 0,
            'error.message' => $this->composeMessage(),
            'error.exception' => $this->getClassName()
        ])->sendResponse();
    }

    /**
     * Compose error message
     * @return string
     */
    private function composeMessage() {
        return $this->staticMessage . $this->message;
    }

    /**
     * Get current class name
     * @return string
     */
    private function getClassName() {
        $name = str_replace('\\', '/', get_class($this));
        if (strpos($name, '/') > 0) {
            $split = explode('/', $name);
            $name = end($split);
        }
        return $name;
    }

    /**
     * Log exception
     * @return GeneralException
     */
    private function logException() {
        if ($this->useLog) {
            try {
                //create message
                $logText = date('Y-m-d H:i:s') . ' - ';
                $logText .= $this->getClassName() . ': ';
                $logText .= $this->getMessage() . ', ';
                $logText .= 'IP: ' . filter_input(INPUT_SERVER, 'REMOTE_ADDR');
                //save message
                App::getFileManager()->saveInStorage('log', $logText);
            } catch (Exception $e) {
                
            } catch (File\FileException $e) {
                
            }
        }
    }

}
