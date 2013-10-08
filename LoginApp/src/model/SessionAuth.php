<?php

namespace model;

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
	 * @return \model\User
	 * @throws \Exception If no user in session is found
	 */
	public function load() {
		if (isset($_SESSION[self::$sessionLocation])) {
			return $_SESSION[self::$sessionLocation];
		}
		throw new \Exception();
	}

	/**
	 * @return bool, true if successfull
	 * @throws Exception If logout is unsuccessfull
	 */
	public function remove() {
		try {
			unset($_SESSION[self::$sessionLocation]);
			return true;
		} catch (\Exception $e) {
			throw new \Exception();
		}
	}

	/**
	 * @param  \model\User $user
	 */
	public function save(\model\User $user) {
		$_SESSION[self::$sessionLocation] = $user;
	}
}