<?php

namespace user\register\model; 

require_once("./src/user/model/UserCredentials.php");
require_once("./src/user/register/model/RegisterObserver.php");

/**
 * @author Peter Emilsson
 */
class Register {

	/**
	 * @var \user\model\UserDAL
	 */
	private $userDAL;
		
	public function __construct() {		
		$this->userDAL = new \user\model\UserDAL();
	}

	/**
	 * @param  \user\model\UserCredentials $fromClient
	 * @param  \user\register\model\RegisterObserver   $observer 
	 * @throws  \Exception if login failed
	 */
	public function doRegister(\user\model\UserCredentials $fromClient, 
							\user\register\model\RegisterObserver $observer) {
		try {
			if (!$this->userDAL->userExists($fromClient)) {
				$this->userDAL->insertUser($fromClient);
				$observer->registerOK();
			} else {
				$observer->userExists();
				throw new \Exception();
			}
		} catch (\Exception $e) {
			throw $e;
		}
	}
}