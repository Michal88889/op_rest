<?php

namespace RestApp\DataProviders;

use RestApp\Exceptions\Database\DatabaseException;

/**
 * Database is a singleton implementation.
 * getConnection() returning an instance of PDO connection.
 * 
 * <code>
 * Example usage:
 * 
 * $pdo = Database::instance()->getConnection($type, $host, $dbName, $userName, $password);
 *    
 * $results = $pdo->query("SELECT * FROM Table");
 * 
 * </code>
 *
 * @author rmurray
 */
class Database {

    /**
     * singleton instance
     * @var PDOConnection 
     */
    protected static $_instance = null;

    /**
     * Returns singleton instance of PDOConnection
     * @return Database 
     */
    public static function instance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new Database();
        }

        return self::$_instance;
    }

    /**
     * Hide constructor, protected so only subclasses and self can use
     */
    protected function __construct() {
        
    }

    function __destruct() {
        
    }

    /**
     * Return a PDO connection using the dsn and credentials provided
     * @param string $type The database type 'mysql' is default
     * @param string $host The database host address
     * @param string $dbName The database name
     * @param string $username Database username
     * @param string $password Database password
     * @return PDO connection to the database
     * @throws DatabaseException
     */
    public function getConnection($type, $host, $dbName, $username, $password) {
        try {
            $conn = new \PDO($type . ':dbname=' . $dbName . ';host=' . $host, $username, $password, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            //Set common attributes
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            throw new DatabaseException('Access denied');
        } catch (Exception $e) {
            throw new DatabaseException('Access denied');
        }
    }

    /** PHP seems to need these stubbed to ensure true singleton * */
    public function __clone() {
        return false;
    }

    public function __wakeup() {
        return false;
    }

}
