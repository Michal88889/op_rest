<?php

namespace RestApp\Request\Post;

use RestApp\Request\HttpRequest;
use RestApp\Request\Post\AddPostRequest;

/*
 * Class responsible for handling post request.
 * This class take proper resources from server and return it to client. 
 */

class PostRequest extends HttpRequest {


    /**
     * HTTP request constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Add post request
     * @return void
     */
    public function addPost() {
        $addPost = new AddPostRequest();
        $addPost->addUserPost($this->getPostParams());
    }

    /**
     * Get post params
     * @return mixed[]
     */
    protected function getPostParams() {
        $result = [];
        foreach ($this->scheme as $paramName => $paramDefault) {
            $param = filter_input(INPUT_POST, $paramName, FILTER_DEFAULT);
            if ($param && !is_null($param)) {
                $result[$paramName] = $param;
            } else {
                $result[$paramName] = $paramDefault;
            }
        }

        return $result;
    }

}
