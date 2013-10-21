<?php

namespace authorization\model;

/**
 * Represents a valid Username
 */
class Username {
	private static $MINIMUM_USERNAME_LENGTH = 3;
	private static $MAXIMUM_USERNAME_LENGTH = 9; 

	private $username;

	/**
	 * @param String $userName
	 * @throws Exception if not ok
	 */
	public function __construct($username) {
		if ($this->isOkUserName($username) == false) {
			throw new \Exception("UserName::__construct : Tried to create user with faulty username");
		}
		$this->username = $username;
	}

	/**
	 * @return String
	 */
	public function __toString() {
		return $this->username;
	}

	/**
	 * @param  String  $string 
	 * @return boolean         
	 */
	private function isOkUserName($string) {
		if (\Common\Filter::hasTags($string) == true) {
			return false;
		} else if (strlen($string) < self::$MINIMUM_USERNAME_LENGTH) {
			return false;
		} else if (strlen($string) > self::$MAXIMUM_USERNAME_LENGTH) {
			return false;
		}
		
		return true;
	}
}