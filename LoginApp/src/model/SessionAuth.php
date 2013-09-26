<?php

namespace Model;

class SessionAuth {

	/**
	 * @var string
	 */
	private static $sessionLocation = "model::UserModel::user";

	/**
	 * Checks if session have started
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
	 * @throws Exception If logout is unsuccessfull
	 */
	public function logout() {
		try {
			unset($_SESSION[self::$sessionLocation]);
			return true;
		} catch (\Exception $e) {
			throw new \Exception("Utloggningen misslyckades");
		}
	}

	/**
	 * @param  \Model\User $user
	 */
	public function login(\Model\User $user) {
		$_SESSION[self::$sessionLocation] = $user;
	}
}