<?php

namespace RestApp\Request\Get;

use RestApp\Request\HttpRequest;
use RestApp\Models\Get\RandomPosts;
use RestApp\Models\Get\LastPosts;
use RestApp\Models\Get\OnlineUsers;

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

    /**
     * Get last posts
     * @return void
     */
    public function lastPosts($number = 100) {
        //get data
        $lastPosts = new LastPosts();
        $posts = $lastPosts->getLastPosts($number);
        //response
        $this->response->setResponse([
            'status' => 1,
            'result' => $posts
        ])->sendResponse();
    }

    /**
     * Get online users
     * @return void
     */
    public function getOnlineUsers() {
        //get data
        $users = new OnlineUsers();
        $data = $users->getOnlineUsers();
        //response
        $this->response->setResponse([
            'status' => 1,
            'result' => $data
        ])->sendResponse();
    }

}
