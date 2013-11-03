<?php

namespace user\model;

require_once("./src/user/model/UserCredentials.php");
require_once("./src/common/model/DbConnection.php");

/**
 * @author Peter Emilsson
 * Class used for user database actions
 */
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
	 * @var \common\model\DbConnection
	 */
	private $dbConnection;

	public function __construct() {
		$this->dbConnection = \common\model\DbConnection::getInstance();
	}

	/**
	 * @param  \user\model\UserCredentials $userCred
	 * @return bool
	 */
	public function userExists(\user\model\UserCredentials $userCred) {
		try {
			$this->getUser($userCred);
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * @param  \user\model\UserCredentials $userCred 
	 * @return \user\model\UserCredentials
	 * @throws \Exception If User not found in database
	 */
	public function getUser(\user\model\UserCredentials $userCred) {
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
			return \user\model\UserCredentials::createFromDbData(
				new \user\model\UserName($username), 
				\user\model\Password::fromEncryptedString($password),
				$id);
		} else {
			throw new \Exception("User not found in database");
		}
	}

	/**
	 * @param  int $id 
	 * @return \user\model\UserCredentials
	 */
	public function getUserById($id) {
		$sql = 'SELECT
				id, 
				username
				FROM ' . self::$userTable . 
				' WHERE id = ?';

		$statement = $this->dbConnection->runSql($sql, array($id), "i");

		$result = $statement->bind_result($id, $username);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}

		if ($statement->fetch()) {
			return \user\model\UserCredentials::createBasic(
				new \user\model\UserName($username), $id);
		} else {
			throw new \Exception("User not found in database");
		}
	}

	/**
	 * @param  \user\model\UserCredentials $userCred
	 */
	public function insertUser(\user\model\UserCredentials $userCred) {
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

	/**
	 * @param  \user\model\UserCredentials $userCred
	 * @param  string                  	   $ip     
	 */
	public function insertTempUser(\user\model\UserCredentials $userCred, $ip) {
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

	/**
	 * @param  string                   $ip       
	 * @param  \user\model\UserCredentials $userCred 
	 * @return \user\model\UserCredentials
	 * @throws \Exception If user not found
	 */
	public function getCookieUser($ip, \user\model\UserCredentials $userCred) {
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
			"sss");

		$result = $statement->bind_result($id, $username, $cookie_expire);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}

		if ($statement->fetch()) {
			return \user\model\UserCredentials::createFromDbCookieData(
												new \user\model\UserName($username), 
												\user\model\Password::emptyPassword(),
												\user\model\TemporaryPasswordServer::createFromDb(
													$cookie_expire, $tempPassword),
												$id);
		} else {
			throw new \Exception("User not found in database");
		}
	}
}