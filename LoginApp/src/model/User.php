<?php

namespace Model;

require_once("./src/model/Crypt.php");

class User {

	/**
	 * @var string
	 */
	private static $correctUsername = "Admin";
	
	/**
	 * Decrypted value: Password
	 * @var string
	 */
	private static $correctPassword = '$2a$07$SBuUNsVHQNpLFNaTfySRcewaojeUNbrf9S/umD5uJ218UL45U1iaq';

	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $ip;

	/**
	 * @var string
	 */
	private $userAgent;

	/**
	 * @var \Model\Crypt
	 */
	private $cryptModel;

	/**
	 * @var CONST
	 */
	private $state;

	/**
	 * Login result
	 */
	CONST successLogin = "0";

	CONST successCookieLogin = "1";

	CONST failedCookieLogin = "2";

	CONST wrongUsernamePassword = "3";

	CONST successLogout = "4";

	CONST successLoginKeep = "5";

	/**
	 * @return String, username
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @return CONST action result
	 */
	public function getState() {
		return $this->state;
	}

	/**
	 * @param string $ip       
	 * @param string $userAgent
	 */
	public function __construct($ip, $userAgent) {
		$this->ip = $ip;
		$this->userAgent = $userAgent;
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
			
			$this->id = 1;
			$this->username = $username;
			$this->state = self::successLogin;
			return true;
		}
		$this->state = self::wrongUsernamePassword; 
		throw new \Exception();
	}

	/**
	 * @return bool, returns true if logged in
	 */
	public function isUserLoggedIn() {
		return $this->id ? true : false;
	}

	/**
	 * @param  \Model\UserDAL $userDAL   
	 * @param  string       $username   
	 * @param  string       $randString 
	 * @param  int       	$time                          
	 */
	public function keepMeLoggedIn(\Model\UserDAL $userDAL, $username, $tempId, $time) {
		$this->userDAL = $userDAL;
		$this->userDAL->insertTempUser($username, $tempId, $time, $this->ip);
		$this->state = self::successLoginKeep;
	}

	/**
	 * Resets the values
	 */
	public function logOut() {
		$this->username = "";
		$this->id = null;
		$this->state = self::successLogout;
	}

	/**
	 * @param  \Model\UserDAL $userDAL 
	 * @param  string      $username
	 * @param  string      $tempId
	 * @throws Exception If cookie has expired                 
	 */
	public function loginByCookies(\Model\UserDAL $userDAL, $username, $tempId) {
		$this->userDAL = $userDAL;

		$cookieExpire = $this->userDAL->getCookieDate($username, $tempId, $this->ip);
		if ($cookieExpire < time()) {
			$this->state = self::failedCookieLogin;
			throw new \Exception();
		}
		$this->id = 1;
		$this->username = $username;
		$this->state = self::successCookieLogin;
	}

	/**
	 * @param  string $ip        
	 * @param  string $userAgent
	 * @return bool       
	 */
	public function compareSession($ip, $userAgent) {
		return $ip == $this->ip && $userAgent == $this->userAgent;
	}

}