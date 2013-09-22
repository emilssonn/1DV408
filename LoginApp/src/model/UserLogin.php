<?php

namespace Model;

class UserLogin {

	/**
	 * @var string
	 */
	private static $sessionLocation = "model::UserModel::user";

	/**
	 * Checks of session have started
	 */
	public function __construct() {
		assert(isset($_SESSION));
	}

	/**
	 * @return \Model\User
	 */
	public function load() {
		if (isset($_SESSION[self::$sessionLocation])) {
			return $_SESSION[self::$sessionLocation];
		}
		return new \Model\User();
	}

	/**
	 * @return bool, true if successfull
	 */
	public function logout() {
		try {
			unset($_SESSION[self::$sessionLocation]);
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * @param  \Model\User $user
	 */
	public function login(\Model\User $user) {
		$_SESSION[self::$sessionLocation] = $user;
	}
}