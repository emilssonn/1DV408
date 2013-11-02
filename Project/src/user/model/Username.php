<?php

namespace user\model;

require_once("./src/common/model/exception/StringLength.php");

/**
 * @author Daniel Toll - https://github.com/dntoll
 * Changes by Peter Emilsson
 * Represents a valid Username
 */
class Username {

	/**
	 * @var integer
	 */
	CONST MINIMUM_USERNAME_LENGTH = 3;

	/**
	 * @var integer
	 */
	CONST MAXIMUM_USERNAME_LENGTH = 9; 

	/**
	 * @var string
	 */
	private $username;

	/**
	 * @param String $userName
	 * @throws Exception if not ok
	 */
	public function __construct($username) {
		$this->isOkUserName($username);
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
		if (\common\Filter::hasTags($string) == true) {
			throw new \Exception();
		} else if (strlen($string) < self::MINIMUM_USERNAME_LENGTH ||
					strlen($string) > self::MAXIMUM_USERNAME_LENGTH) {
			throw new \common\model\exception\StringLength(self::MINIMUM_USERNAME_LENGTH, self::MAXIMUM_USERNAME_LENGTH);
		}
	}
}