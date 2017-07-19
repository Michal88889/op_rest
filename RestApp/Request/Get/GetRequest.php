<?php

namespace RestApp\Request\Get;

use RestApp\Request\HttpRequest;
use RestApp\Models\Get\RandomPosts;

/*
 * Class responsible for handling get request.
 * This class take proper resources from server and return it. 
 */

class GetRequest extends HttpRequest {

    /**
     * HTTP request constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Random post of bot handle
     * @return void
     */
    public function randomPost() {
        //get data
        $randomPosts = new RandomPosts();
        $post = $randomPosts->getRandomPost();
        //response
        $this->response->setResponse([
            'status' => 1,
            'result' => $post
        ])->sendResponse();
    }

}
