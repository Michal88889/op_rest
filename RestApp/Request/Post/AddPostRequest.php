<?php

namespace RestApp\Request\Post;

use RestApp\Request\Validator;
use RestApp\Request\HttpRequest;
use RestApp\Models\Post\AddPost;

/*
 * Class responsible for handling add post request.
 * This class take proper resources from server and return it to client. 
 */

class AddPostRequest extends HttpRequest {

    /**
     * Structure of table
     * @var mixed[] 
     */
    protected $scheme = [
        'userID' => 0,
        'userName' => '',
        'userRole' => 0,
        'channel' => 0,
        'dateTime' => '',
        'ip' => '',
        'text' => ''
    ];

    /**
     * HTTP request constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Add user post to chat
     * @param mixed[] $params
     * @return void
     */
    public function addUserPost($params) {
        $addPost = new AddPost();

        //set default
        $params['dateTime'] = date('Y-m-d H:i:s');
        $params['ip'] = $this->packIp(filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_DEFAULT));

        //validate
        $validate = $this->validateAddPost($params);
        $status = $validate['valid'];

        //add if valid
        if ($status) {
            $add = $addPost->addPost($params);
            $status &= $add;
        }

        $params['ip'] = $this->unpackIp($params['ip']);
        //response
        $this->response->setResponse([
            'status' => (int) $status,
            'message' => $validate['error'],
            'result' => $params
        ])->sendResponse();
    }

    /**
     * Validate add post request
     * @return mixed[]
     */
    protected function validateAddPost($params) {
        $validator = new Validator($params);

        $validator->addRules('userID', ['isset', 'isNumeric', '>0']);
        $validator->addRules('userName', ['isset', 'isNotEmpty']);
        $validator->addRules('userRole', ['isset', 'isNumeric', '>0']);
        $validator->addRules('channel', ['isset', 'isNumeric']);
        $validator->addRules('dateTime', ['isset', 'isNotEmpty']);
        $validator->addRules('ip', ['isset', 'isNotEmpty']);
        $validator->addRules('text', ['isset', 'isNotEmpty']);

        $valid = $validator->validate();
        $error = $validator->getFirstErrorMessage();
        return [
            'valid' => $valid,
            'error' => $error ? $error : '',
        ];
    }

}
