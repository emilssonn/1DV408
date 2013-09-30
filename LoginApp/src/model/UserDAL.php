<?php

namespace Model;

class UserDAL {

	/**
	 * @var string
	 */
	private static $tempUserTable = "temp_user";

	/**
	 * @var mysqli
	 */
	private $mysqli;

	/**
	 * @param mysqli $mysqli
	 */
	public function __construct(\mysqli $mysqli) {
		$this->mysqli = $mysqli;
	}

	/**
	 * @param  string $username   [description]
	 * @param  string $tempID     [description]
	 * @param  string $cookieDate [description]
	 * @param  string $ip         [description]
	 */
	public function insertTempUser($username, $tempID, $cookieDate, $ip) {
		$sql = "INSERT INTO " . self::$tempUserTable . "
				(
					username,
					temp_id,
					ip,
					cookie_expire
				)
				VALUES(?, ?, ? ,?)";

		//http://www.php.net/manual/en/mysqli-stmt.prepare.php
		$statement = $this->mysqli->prepare($sql);
		if ($statement === FALSE) {
			throw new \Exception("prepare of $sql failed " . $this->mysqli->error);
		}

		//http://www.php.net/manual/en/mysqli-stmt.bind-param.php
		if ($statement->bind_param("ssss", $username, $tempID, $ip, $cookieDate) === FALSE) {
				throw new \Exception("bind_param of $sql failed " . $statement->error);
		}

		//http://www.php.net/manual/en/mysqli-stmt.execute.php
		if ($statement->execute() === FALSE) {
			throw new \Exception("execute of $sql failed " . $statement->error);
		}
	}

	/**
	 * @param  string $username [description]
	 * @param  string $tempID   [description]
	 * @param  string $ip       [description]
	 * @return string           [description]
	 */
	public function findTempUser($username, $tempID, $ip) {
		$sql = 'SELECT
				cookie_expire FROM ' . self::$tempUserTable . 
				' WHERE username = "' . $username . 
				'" AND temp_id = "' . $tempID . 
				'" AND ip = "' . $ip . '";';

		$statement = $this->mysqli->prepare($sql);

		if ($statement === FALSE) {
			throw new \Exception("prepare of $sql failed " . $this->mysqli->error);
		}

		if ($statement->execute() === FALSE) {
			throw new \Exception("execute of $sql failed " . $statement->error);
		}

		$result = $statement->get_result();

		$object = $result->fetch_array(MYSQLI_ASSOC);

		return $object["cookie_expire"];
		
	}
}