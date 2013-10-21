<?php

namespace authorization\model;

require_once("./src/user/model/UserCredentials.php");
require_once("./src/common/model/DbConnection.php");

class UserDAL {

	/**
	 * @var string
	 */
	private static $tempUserTable = "temp_user";

	/**
	 * @var string
	 */
	private static $userTable = "user";

	/**
	 * @var mysqli
	 */
	private $mysqli;

	/**
	 * @param mysqli $mysqli
	 */
	public function __construct(\mysqli $mysqli) {
		$this->mysqli = $mysqli;
		$this->dbConnection = new \common\model\DbConnection($this->mysqli);
	}

	public function userExists(\authorization\model\UserCredentials $userCred) {
		try {
			$this->getUser($userCred);
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function getUser(\authorization\model\UserCredentials $userCred) {
		$sql = 'SELECT
				id, 
				username, 
				password 
				FROM ' . self::$userTable . 
				' WHERE username = ?';

		$statement = $this->dbConnection->runSql($sql, array($userCred->getUsername()), "s");

		$result = $statement->bind_result($id, $username, $password);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}

		if ($statement->fetch()) {
			return \authorization\model\UserCredentials::createFromDbData(
				new \authorization\model\UserName($username), 
				\authorization\model\Password::fromEncryptedString($password),
				$id);
		} else {
			throw new \Exception("User not found in database");
		}
	}

	public function insertUser(\authorization\model\UserCredentials $userCred) {
		$sql = "INSERT INTO " . self::$userTable . "
				(
					username,
					password
				)
				VALUES(?, ?)";

		$statement = $this->dbConnection->runSql($sql, 
			array($userCred->getUsername(), $userCred->getPassword()->getEncryptedPassword()), 
			"ss");
	}

	public function insertTempUser(\authorization\model\UserCredentials $userCred, $ip) {
		$sql = "INSERT INTO " . self::$tempUserTable . "
				(
					id, 
					username,
					temp_token,
					ip,
					cookie_expire
				)
				VALUES(?, ?, ? ,?, ?)";

		$params = array($userCred->id, $userCred->getUsername(), 
			$userCred->getTemporaryPassword()->getTemporaryPassword(), $ip,
			$userCred->getTemporaryPassword()->getExpireDate());

		$statement = $this->dbConnection->runSql($sql, $params, "issss");
	}

	public function getCookieUser($ip, \authorization\model\UserCredentials $userCred) {
		$tempPassword = $userCred->getTemporaryPassword()->getTemporaryPassword();
		$sql = 'SELECT
				id,
				username,
				cookie_expire FROM ' . self::$tempUserTable . 
				' WHERE username = ?
				  AND temp_token = ? 
				  AND ip = ?';

		$statement = $this->dbConnection->runSql($sql, 
			array($userCred->getUsername(), $tempPassword, $ip), 
			"ssi");

		$result = $statement->bind_result($id, $username, $cookie_expire);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}

		if ($statement->fetch()) {
			return \authorization\model\UserCredentials::createFromDbCookieData(
												new \authorization\model\UserName($username), 
												\authorization\model\Password::emptyPassword(),
												\authorization\model\TemporaryPasswordServer::createFromDb(
													$cookie_expire, $tempPassword),
												$id);
		} else {
			throw new \Exception("User not found in database");
		}
	}
}