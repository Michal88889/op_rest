<?php

namespace RestApp\Models\Get;

use RestApp\Models\Model;

/*
 * Model for collecting random post data from database
 */

class LastPosts extends Model {

    /**
     * HTTP request constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Get random post ID
     * @return int
     */
    public function getLastPosts($number = 100) {
        $query = "SELECT * FROM (SELECT id, userID, userName, userRole, dateTime, text from ajax_chat_messages WHERE text NOT LIKE '/%' ORDER BY id desc LIMIT 0, :max) as chat_table order by id asc";
        $stm = $this->db->prepare($query);

        $stm->bindParam(':max', $number);
        
        $stm->execute();
        return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }
}
