<?php

namespace user\model;

/**
 * @author Daniel Toll - https://github.com/dntoll
 * Changes by Peter Emilsson
 * The client representation of a temporary password
 */
class TemporaryPasswordClient extends TemporaryPassword{
	/**
	 * note: Private since it is called with fromCookie
	 * @param String $cookieString 
	 */
	private function __construct($cookieString) {
		$this->temporaryPassword = $cookieString;
	}
	
	/**
	 * @param  String $cookieString 
	 * @return TemporaryPasswordClient
	 */
	public static function fromString($cookieString) {
		return new TemporaryPasswordClient($cookieString);
	}
	
	/**
	 * @return TemporaryPasswordClient
	 */
	public static function emptyPassword() {
		return new TemporaryPasswordClient("");
	}
}
