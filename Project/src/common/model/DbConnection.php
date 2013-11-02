<?php

namespace common\model;

/**
 * @author Peter Emilsson
 * Final class for database operations
 * This class should not and can not be extended
 * This class is a singleton class.
 * It can only be created once.
 */
final class DbConnection {

	/**
	 * @var \common\model\DbConnection
	 */
	private static $instance = null;

	/**
	 * @var \mysqli
	 */
	private $mysqli;

	/**
	 * Returns the created instace of the class, or create one if none exists
	 * @return \common\model\DbConnection
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
	}

	/**
	 * Prevent from being used from outside
	 */
	private function __construct() {
		
	}

	/**
	 * Prevent from being used from outside
	 */
	private function __clone() {

	}

	/**
	 * Connect to the database
	 * @param  string $dbServer   
	 * @param  string $dbUser     
	 * @param  string $dbPassword 
	 * @param  string $db         
	 * @throws \Exception If connection to database failed
	 */
	public function connect($dbServer, $dbUser, $dbPassword, $db) {
		$this->mysqli = new \mysqli($dbServer, $dbUser, $dbPassword, $db);
		if ($this->mysqli->connect_errno) {
   			throw new \Exception("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		}
	}

	/**
	 * Close connection to database
	 */
	public function close() {
		$this->mysqli->close();
	}

	/**
	 * @param  string $sql, sql query to be used
	 * @param  mixed  $paramsArray, parameters to be used     
	 * @param  string $paramsTypeString, parameters type
	 * @return \mysqli statement
	 * @throws \Exception If query fails
	 */
	public function runSql($sql, $paramsArray = null, $paramsTypeString = null) {
		$statement = $this->mysqli->prepare($sql);
		if ($statement === FALSE) {
			throw new \Exception("prepare of $sql failed " . $this->mysqli->error);
		}

		if ($paramsArray !== null) {
			$params = array_merge(array($paramsTypeString), $paramsArray);

			if (call_user_func_array(array($statement, "bind_param"), $this->refValues($params)) === FALSE) {
				throw new \Exception("bind_param of $sql failed " . $statement->error);
			}
		}

		if ($statement->execute() === FALSE) {
			throw new \Exception("execute of $sql failed " . $statement->error);
		}

		return $statement;
	}

	/**
	 * Return id from last insertion
	 * @return int
	 */
	public function getLastInsertedId() {
		return $this->mysqli->insert_id;
	}

	/**
	 * @Source: http://php.net/manual/en/mysqli-stmt.bind-param.php#96770
	 * @param  array $arr
	 * @return array
	 */
	private function refValues($arr) { 
        $refs = array();

        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key]; 
        }

        return $refs; 
	}
}