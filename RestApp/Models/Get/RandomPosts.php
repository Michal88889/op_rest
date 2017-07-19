<?php

namespace RestApp\Models\Get;

use RestApp\Models\Model;
use RestApp\Exceptions\Database\DatabaseException;

/*
 * Model for collecting random post data from database
 */

class RandomPosts extends Model {

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
    public function getRandomPostId() {
        $query = "SELECT FLOOR(RAND() * max(id)) as 'rnd' from ajax_chat_messages";
        try {
            $stm = $this->db->prepare($query);
            $stm->execute();
            $row = $stm->fetch();
        } catch (\PDOException $e) {
            DatabaseException::capture($e)->sendResponse();
        } catch (Exception $e) {
            DatabaseException::capture($e)->sendResponse();
        }
        return $row['rnd'];
    }

    /**
     * Get random post from database
     * @return mixed[]|false
     */
    public function getRandomPost() {
        $randMax = $this->getRandomPostId();
        try {
            $randMin = (rand() / getrandmax()) * $randMax;
            $query = "SELECT id, userID, userName, userRole, channel, dateTime, text FROM ajax_chat_messages WHERE id between :min and :max and userName NOT LIKE 'ChatBot' and userName not LIKE 'Korwin Krul' and text NOT LIKE '/%' and text NOT LIKE '%krul%' and text NOT LIKE '%Krul%' and LENGTH(text) < 120 LIMIT 0, 1";
            $stm = $this->db->prepare($query);

            $stm->bindParam(':min', $randMin);
            $stm->bindParam(':max', $randMax);

            $stm->execute();
            $data = $stm->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            DatabaseException::capture($e)->sendResponse();
        } catch (Exception $e) {
            DatabaseException::capture($e)->sendResponse();
        }
        return $data;
    }

}
