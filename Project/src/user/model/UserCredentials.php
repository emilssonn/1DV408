<?php

namespace user\model;

require_once("./src/user/model/TemporaryPassword.php");
require_once("./src/user/model/TemporaryPasswordServer.php");
require_once("./src/user/model/TemporaryPasswordClient.php");
require_once("./src/user/model/Username.php");
require_once("./src/user/model/Password.php");

/**
 * @author Daniel Toll - https://github.com/dntoll
 * Changes by Peter Emilsson
 */
class UserCredentials {

	public $id;

	private $username;

	private $password;

	private $temporaryPassword;
	

	/**
	 * @param UserName string
	 * @param Password string 
	 * @param TemporaryPassword $temporaryPassword
	 */
	private function __construct(\user\model\Username $username, 
								 \user\model\Password $password,	 
								 \user\model\TemporaryPassword $temporaryPassword,
								 $id = null) {
		$this->username = $username;
		$this->password = $password;
		$this->id = $id;
		$this->temporaryPassword = $temporaryPassword;
	}

	public static function createFromDbData(\user\model\Username $username, \user\model\Password $password, $id) {
		return new UserCredentials($username, $password, new \user\model\TemporaryPasswordServer(), $id);
	}

	public static function createFromDbCookieData(	\user\model\Username $username, \user\model\Password $password, 
													\user\model\TemporaryPasswordServer $temporaryPasswordServer, 
													$id) {
		return new UserCredentials($username, $password, $temporaryPasswordServer, $id);
	}

	public static function createBasic(\user\model\Username $username, $id) {
		return new UserCredentials($username, \user\model\Password::emptyPassword(), new \user\model\TemporaryPasswordServer(), $id);
	}

	public static function create(\user\model\Username $username, \user\model\Password $password) {
		return new UserCredentials($username, $password, new \user\model\TemporaryPasswordServer());
	}

	public static function createFromClientData(\user\model\Username $username, 
												\user\model\Password $password) {
		return new UserCredentials($username, $password, \user\model\TemporaryPasswordClient::emptyPassword());
	}

	public static function createWithTempPassword(\user\model\Username $username, 
												  \user\model\TemporaryPasswordClient $temporaryPassword) {
		return new UserCredentials($username, \user\model\Password::emptyPassword(), $temporaryPassword);
	}
	
	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	public function gotPassword() {
		return $this->password->__toString() != null;
	}

	public function getPassword() {
		return $this->password;
	}
	
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return TemporaryPassword
	 */
	public function getTemporaryPassword() {
		return $this->temporaryPassword;
	}
	
	public function newTemporaryPassword() {
		$this->temporaryPassword = new \user\model\TemporaryPasswordServer();
	}
	
	/**
	 * @param  UserCredentials $other 
	 * @return boolean        
	 */
	public function isSame(\user\model\UserCredentials $userCred) {
		$usernameIsSame = $this->getUsername() == $userCred->getUsername();
		$passwordIsSame = false;
		if ($userCred->password != null)
			$passwordIsSame = $this->password->isSame($userCred->password);
		
		$tempPasswordsMatch = $this->temporaryPassword->doMatch($userCred->temporaryPassword);	
		
		if ($usernameIsSame && ($passwordIsSame || $tempPasswordsMatch)) {
			return true;
		}
		return false;
	}
}