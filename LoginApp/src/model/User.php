<?php

namespace model;

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
	 * @var \model\Crypt
	 */
	private $cryptModel;

	/**
	 * @var string
	 */
	private $username;

	private $tempId;

	/**
	 * @return String, username
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param string $ip       
	 * @param string $userAgent
	 */
	public function __construct(\model\UserDAL $userDAL, $ip, $userAgent) {
		$this->userDAL = $userDAL;
		$this->ip = $ip;
		$this->userAgent = $userAgent;
		$this->cryptModel = new \model\Crypt();
	}

	/**
	 * @param String $username
	 * @param String $password
	 * @return bool, returns true if successfull
	 * @throws Exception If username or password is not correct
	 */
	public function login(\model\LoginObserver $loginObserver, $username, $password) {

		if ($username == self::$correctUsername && 
			crypt($password, self::$correctPassword) == self::$correctPassword) {
			
			$this->id = 1;
			$this->username = $username;
			$loginObserver->okFormLogin();
			return true;
		}
		$loginObserver->wrongUserCredentials();
		throw new \Exception('Wrong username or password');
	}

	/**
	 * @return bool, returns true if logged in
	 */
	public function isUserLoggedIn() {
		return $this->id ? true : false;
	}

	/**
	 * @param  \model\UserDAL $userDAL   
	 * @param  string       $username   
	 * @param  string       $randString 
	 * @param  int       	$time                          
	 */
	public function keepMeLoggedIn(\model\LoginObserver $loginObserver, $username, $time) {
		$this->tempId = $this->cryptModel->crypt(time());
		$this->userDAL->insertTempUser($username, $this->tempId, $time, $this->ip);
		$loginObserver->okKeepMeLoggedIn();
	}

	public function getTempId() {
		return $this->tempId;
	}

	/**
	 * Resets the values
	 */
	public function logOut(\model\LoginObserver $loginObserver) {
		$loginObserver->okLogOut();
	}

	/**
	 * @param  \model\UserDAL $userDAL 
	 * @param  string      $username
	 * @param  string      $tempId
	 * @throws Exception If cookie has expired                 
	 */
	public function loginByCookies(\model\LoginObserver $loginObserver, $username, $tempId) {

		$cookieExpire = $this->userDAL->getCookieDate($username, $tempId, $this->ip);
		if ($cookieExpire < time()) {
			$loginObserver->failedCookieLogin();
			throw new \Exception('Cookie expire date do not match');
		}
		$this->id = 1;
		$this->username = $username;
		$loginObserver->okCookieLogin();
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