<?php

namespace form\model;

class PersistantForm {

	/**
	 * @var string
	 */
	private static $sessionLocation = "Form::Model::PersistantForm";

	/**
	 * Checks if session have started
	 */
	public function __construct() {
		assert(isset($_SESSION));
	}

	public function load() {
		if (isset($_SESSION[self::$sessionLocation])) {
			return $_SESSION[self::$sessionLocation];
		}
		throw new \Exception();
	}

	public function remove() {
		try {
			unset($_SESSION[self::$sessionLocation]);
			return true;
		} catch (\Exception $e) {
			throw new \Exception();
		}
	}

	public function save(\form\model\Form $form) {
		$_SESSION[self::$sessionLocation] = $form;
	}
}