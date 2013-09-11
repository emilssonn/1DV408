<?php

namespace Model;

class UserLogin {

	/**
	 * @var string
	 */
	private static $sessionLocation = "model::UserModel::user";

	/**
	 * [__construct description]
	 */
	public function __construct() {
		assert(isset($_SESSION));
	}

	/**
	 * [login description]
	 * @return [type] [description]
	 */
	public function login() {
		if (isset($_SESSION[self::$sessionLocation])) {
			return $_SESSION[self::$sessionLocation];
		}
		return new User();
	}

	/**
	 * [logout description]
	 * @return [type] [description]
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
	 * [saveUser description]
	 * @param  ModelUser $user [description]
	 * @return [type]          [description]
	 */
	public function saveUser(\Model\User $user) {
		$_SESSION[self::$sessionLocation] = $user;
	}
}