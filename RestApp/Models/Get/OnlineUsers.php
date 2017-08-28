<?php

namespace RestApp\Models\Get;

use RestApp\Models\Model;

/*
 * Model for collecting random post data from database
 */

class OnlineUsers extends Model {

    /**
     * HTTP request constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Get online users
     * @return int
     */
    public function getOnlineUsers() {
        $query = "SELECT userID, userName, userRole, channel, dateTime from ajax_chat_online";
        $stm = $this->db->prepare($query);
        
        $stm->execute();
        return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }
}
