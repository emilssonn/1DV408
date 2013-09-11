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
	 * [$isUserLoggedIn description]
	 * @var boolean
	 */
	private $isUserLoggedIn = false;

	/**
	 * @param String $username
	 * @param String $password
	 */
	public function __construct($username = null, $password = null) {
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * [login description]
	 * @return bool returns true of successfull
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
	 * [getUsername description]
	 * @return [type] [description]
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * [isUserLoggedIn description]
	 * @return boolean [description]
	 */
	public function isUserLoggedIn() {
		return $this->isUserLoggedIn;
	}

	/**
	 * [logout description]
	 * @return [type] [description]
	 */
	public function logout() {
		$this->username = "";
		$this->password = "";
	}

}