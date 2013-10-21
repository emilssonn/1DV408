<?php

namespace register\model; 

require_once("./src/user/model/UserCredentials.php");
require_once("./src/user/register/model/RegisterObserver.php");

class Register {

	private $userDAL;
		
	public function __construct(\mysqli $mysqli) {		
		$this->userDAL = new \authorization\model\UserDAL($mysqli);
	}

	/**
	 * @param  UserCredentials $fromClient
	 * @param  LoginObserver   $observer 
	 *
	 * @throws  \Exception if login failed
	 */
	public function doRegister(\authorization\model\UserCredentials $fromClient, 
							\register\model\RegisterObserver $observer) {
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