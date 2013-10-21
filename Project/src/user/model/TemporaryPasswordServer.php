<?php

namespace authorization\model;

/**
 * The server representation of a temporary password
 * adds expireDate 
 */
class TemporaryPasswordServer extends TemporaryPassword {
	
	/**
	 * @var String
	 */
	protected $temporaryPassword;
	
	/**
	 * @var int Unix Timestamp
	 */
	private $expireDate;
	
	
	public function __construct() {
		$this->expireDate = time() + 60;
		$this->temporaryPassword = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 40);
	}

	/**
	 * @return int Unix Timestamp
	 */
	public function getExpireDate() {
		return $this->expireDate;
	}

	
	/**
	 * @param  String $serverString
	 * @return TemporaryPasswordServer
	 */
	public static function createFromDb($expire, $tempPassword) {
		$ret = new TemporaryPasswordServer();
		$ret->temporaryPassword = $tempPassword;
		$ret->expireDate = $expire;
		return $ret;
	}
		
	/**
	 * @param  TemporaryPasswordClient $fromCookie
	 * @return boolean 
	 */
	public function doMatch(TemporaryPasswordClient $fromCookie) {
		
		if (time() > $this->expireDate) {
			return false;
		}

		if (strcmp($this->temporaryPassword, $fromCookie->temporaryPassword) != 0){
			return false;
		}
		
		return true;
	}
}
