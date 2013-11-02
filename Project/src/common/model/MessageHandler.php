<?php

namespace common\model;

/**
 * @author Peter Emilsson
 * Saves message keys in session
 */
class MessageHandler {

	/**
	 * @var string
	 */
	private static $sessionLocation = "common::model::MessageHandler";

	/**
	 * Checks if session have started
	 */
	public function __construct() {
		assert(isset($_SESSION));
	}

	/**
	 * Return all message keys in array, empty array if none
	 * @return array of int
	 */
	public function load() {
		if (isset($_SESSION[self::$sessionLocation])) {
			return $_SESSION[self::$sessionLocation];
		}
		return array();
	}

	/**
	 * Remove all messages from session
	 * @throws \Exception If unset fails
	 */
	public function removeAll() {
		try {
			unset($_SESSION[self::$sessionLocation]);
		} catch (\Exception $e) {
			throw new \Exception();
		}
	}

	/**
	 * @param int $messageId
	 */
	public function addMessage($messageId) {
		if (isset($_SESSION[self::$sessionLocation])) {
			$_SESSION[self::$sessionLocation][] = $messageId;
		} else {
			$_SESSION[self::$sessionLocation] = array($messageId);
		}
	}
}