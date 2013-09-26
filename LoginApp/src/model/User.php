<?php

namespace Model;

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
	 * @return String. returns the password
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param String $password, sets the password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * @param String $username, not required
	 * @param String $password, not required
	 */
	public function __construct($username = null, $password = null) {
		$this->setUsername($username);
		$this->setPassword($password);
	}

	/**
	 * @param String $username
	 * @param String $password
	 * @return bool, returns true if successfull
	 * @throws Exception If username or password is not correct
	 */
	public function login() {

		if ($this->username == self::$correctUsername && 
			$this->password == self::$correctPassword) {
			
			$this->isUserLoggedIn = true;
			return true;
		} 
		throw new \Exception("Felaktigt användarnamn och/eller lösenord");
	}

	/**
	 * @return boolean, returns true if logged in
	 */
	public function isUserLoggedIn() {
		return $this->isUserLoggedIn;
	}

	/**
	 * Resets the values
	 */
	public function logOut() {
		$this->setUsername("");
		$this->setPassword("");
		$this->isUserLoggedIn = false;
	}

}