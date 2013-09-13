<?php

namespace Model;

/**
 * 
 */
class User {

	/**
	 * @var string
	 */
	private static $correctUsername = "Admin";
	
	/**
	 * @var string
	 */
	private static $correctPassword = "Password";

	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var boolean
	 */
	private $isUserLoggedIn = false;

		/**
	 * @return String, return the username
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param String $username, sets the username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * @param String $username, not required
	 * @param String $password, not required
	 */
	public function __construct($username = null, $password = null) {
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * @return bool, returns true if successfull
	 */
	public function login($username, $password) {
		$this->username = $username;
		$this->password = $password;

		if ($this->username == self::$correctUsername && $this->password == self::$correctPassword) {
			$this->isUserLoggedIn = true;
			return true;
		} 
		return false;
	}

	/**
	 * @return boolean, returns true if logged in
	 */
	public function isUserLoggedIn() {
		return $this->isUserLoggedIn;
	}

	/**
	 * @return [type] [description]
	 */
	public function logout() {
		$this->username = "";
		$this->password = "";
	}

}