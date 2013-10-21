<?php

namespace authorization\model;

require_once("./vendor/Crypt.php");

class Password {
	private static $MINIMUM_PASSWORD_CHARACTERS = 6;
	private static $MAXIMUM_PASSWORD_CHARACTERS = 16;

	/**
	 * @var String
	 */
	private $password;

	private $encryptedPassword;

	private $cryptModel;

	private function __construct() {
		$this->cryptModel = new \model\Crypt();
	}

	/**
	 * Create password from encrypted String
	 * @param  String $encryptedPassword
	 * @return Password
	 */
	public static function fromEncryptedString($encryptedPassword) {
		$ret = new Password();
		$ret->password = $encryptedPassword;
		return $ret;	
		
	}

	/**
	 * Create password from cleartext string
	 * @param  String $password
	 * @return Password
	 */
	public static function fromCleartext($cleartext) {
		if (self::isOkPassword($cleartext) == true ) {
			$ret = new Password();
			$ret->password = $cleartext;
			$ret->encryptedPassword = $ret->encryptPassword($cleartext);
			return $ret;
		} 
		throw new \Exception("Tried to create user with faulty password");
	}

	/**
	 * Create empty/nonvalid password 
	 * @return Password
	 */
	public static function emptyPassword() {
		return new Password();
	}

	/**
	 * @return String
	 */
	public function __toString() {
		return $this->password;
	}

	public function getEncryptedPassword() {
		return $this->encryptedPassword;
	}

	public function isSame(Password $password) {
		return crypt($password->password, $this->password) == $this->password;
	}

	/**
	 * @param  String  $string 
	 * @return boolean         
	 */
	private static function isOkPassword($string) {
		
		if (strlen($string) < self::$MINIMUM_PASSWORD_CHARACTERS) {
			return false;
		} else if (strlen($string) > self::$MAXIMUM_PASSWORD_CHARACTERS) {
			return false;
		}
		return true;
	}
	
	/**
	 * @param  String $rawPassword 
	 * @return String              
	 */
	private function encryptPassword($rawPassword) {
		return $this->cryptModel->crypt($rawPassword);
	}
}