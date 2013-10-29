<?php

namespace common\model;

final class DbConnection {

	private static $instance = null;
	private $mysqli;

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

	public function connect($dbServer, $dbUser, $dbPassword, $db) {
		$this->mysqli = new \mysqli($dbServer, $dbUser, $dbPassword, $db);
		if ($this->mysqli->connect_errno) {
   			throw new \Exception("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		}
	}

	public function close() {
		$this->mysqli->close();
	}

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

	public function getLastInsertedId() {
		return $this->mysqli->insert_id;
	}

	/**
	 * Source: http://php.net/manual/en/mysqli-stmt.bind-param.php#96770
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