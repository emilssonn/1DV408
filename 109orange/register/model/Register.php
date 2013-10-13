<?php

namespace register\model; 

require_once("./login/model/UserCredentials.php");
require_once("./login/model/UserList.php");
require_once("./register/model/RegisterObserver.php");

class Register {

	/**
	 * @var \model\UserList
	 */
	private $allUsers;
		
	public function __construct() {		
		$this->allUsers = new \login\model\UserList();
	}

	/**
	 * @param  UserCredentials $fromClient
	 * @param  LoginObserver   $observer 
	 *
	 * @throws  \Exception if login failed
	 */
	public function doRegister(\login\model\UserCredentials $fromClient, 
							\register\model\RegisterObserver $observer) {
		try {
			if (!$this->allUsers->userExists($fromClient)) {
				//create new temporary password and save it
				$fromClient->newTemporaryPassword();
				//this user needs to be saved since temporary password changed
				$this->allUsers->update($fromClient);

				$observer->registerOK($fromClient->getTemporaryPassword());
			} else {
				\Debug::log("Registration failed, user already exists");
				$observer->userExists();
				throw new \Exception();
			}
		} catch (\Exception $e) {
			throw $e;
		}
	}
}