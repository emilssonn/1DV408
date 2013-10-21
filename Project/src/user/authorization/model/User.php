<?php

namespace authorization\model;

class User {

	/**
	 * @var UserCredentials
	 */
	public $user;

	/**
	 * @var String
	 */
	public $ipAdress;

	/**
	 * @var String
	 */
	public $userAgent;

	
	/**
	 * Only called at login and then saved in session
	 * @param UserCredentials $user      logged in user
	 */
	public function __construct(UserCredentials $user) {
		$this->user = $user;
		$this->ipAdress = $_SERVER["REMOTE_ADDR"];
		$this->userAgent = $_SERVER["HTTP_USER_AGENT"];
	}

	/**
	 * Is it the same session as we logged into
	 * 
	 * @param  String $ipAdress  
	 * @param  String $userAgent 
	 * @return Boolean           
	 */
	public function isSameSession() {
		if($this->ipAdress != $_SERVER["REMOTE_ADDR"] || 
			$this->userAgent != $_SERVER["HTTP_USER_AGENT"]) {
			return false;
		}
		return true;
	}
}