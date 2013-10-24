<?php

namespace authorization\model;

require_once("./src/user/model/TemporaryPassword.php");
require_once("./src/user/model/TemporaryPasswordServer.php");
require_once("./src/user/model/TemporaryPasswordClient.php");
require_once("./src/user/model/Username.php");
require_once("./src/user/model/Password.php");

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
	private function __construct(Username $username, 
								 Password $password,	 
								 TemporaryPassword $temporaryPassword,
								 $id = null) {
		$this->username = $username;
		$this->password = $password;
		$this->id = $id;
		$this->temporaryPassword = $temporaryPassword;
	}

	public static function createFromDbData(Username $username, Password $password, $id) {
		return new UserCredentials($username, $password, new TemporaryPasswordServer(), $id);
	}

	public static function createFromDbCookieData(	Username $username, Password $password, 
													TemporaryPasswordServer $temporaryPasswordServer, 
													$id) {
		return new UserCredentials($username, $password, $temporaryPasswordServer, $id);
	}

	

	public static function create(Username $username, Password $password) {
		return new UserCredentials($username, $password, new TemporaryPasswordServer());
	}

	public static function createFromClientData(Username $username, 
												Password $password) {
		return new UserCredentials($username, $password, TemporaryPasswordClient::emptyPassword());
	}

	public static function createWithTempPassword(Username $username, 
												  TemporaryPasswordClient $temporaryPassword) {
		return new UserCredentials($username, Password::emptyPassword(), $temporaryPassword);
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
		$this->temporaryPassword = new TemporaryPasswordServer();
	}
	
	/**
	 * @param  UserCredentials $other 
	 * @return boolean        
	 */
	public function isSame(UserCredentials $userCred) {
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