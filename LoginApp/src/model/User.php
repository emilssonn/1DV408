<?php

namespace Model;

require_once("./src/model/Crypt.php");

class User {

	private $cryptModel;

	/**
	 * @var string
	 */
	private static $correctUsername = "Admin";
	
	/**
	 * @var string
	 */
	private static $correctPassword = '$2a$07$SBuUNsVHQNpLFNaTfySRcewaojeUNbrf9S/umD5uJ218UL45U1iaq';//Password

	/**
	 * @var string
	 */
	private $username;

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
	 * @param String $username, not required
	 * @param String $password, not required
	 */
	public function __construct() {
		$this->cryptModel = new \Model\Crypt();
	}

	/**
	 * @param String $username
	 * @param String $password
	 * @return bool, returns true if successfull
	 * @throws Exception If username or password is not correct
	 */
	public function login($username, $password) {

		if ($username == self::$correctUsername && 
			crypt($password, self::$correctPassword) == self::$correctPassword) {
			
			$this->isUserLoggedIn = true;
			$this->username = $username;
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
		$this->username = "";
		$this->isUserLoggedIn = false;
	}

	public function loginByCookies(\Model\UserDAL $userDAL, $username, $tempID, $ip) {
		$this->userDAL = $userDAL;
		try {
			$cookieExpire = $this->userDAL->findTempUser($username, $tempID, $ip);
			if ($cookieExpire < time()) {
				throw new \Exception();
			}
			$this->isUserLoggedIn = true;
			$this->username = $username;
			return true;
		} catch(\Exception $e) {
			throw new \Exception();
		}
		
	}

}