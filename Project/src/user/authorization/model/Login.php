<?php

namespace authorization\model;

require_once("./src/user/authorization/model/SessionAuth.php");
require_once("./src/user/model/UserDAL.php");
require_once("./src/user/model/UserCredentials.php");

class Login {
	
	private $sessionAuthModel;

	private $userDAL;

	private $ip;
	
	public function __construct() {
		$this->sessionAuthModel = new \authorization\model\SessionAuth();
		$this->userDAL = new \authorization\model\UserDAL();
		$this->ip = $_SERVER["REMOTE_ADDR"];
	}
	
	public function doLogin(\authorization\model\UserCredentials $userCred, 
							\authorization\model\LoginObserver $loginObserver) {

		try {
			$dbUserCred;
			if ($userCred->gotPassword()) {
				$dbUserCred = $this->userDAL->getUser($userCred);
			} else {
				$dbUserCred = $this->userDAL->getCookieUser($this->ip, $userCred);
			}
			
			if ($dbUserCred->isSame($userCred)) {
				$this->setLoggedIn($dbUserCred);
				$loginObserver->loginOK($dbUserCred->getTemporaryPassword());
				return $dbUserCred;
			} else {
				throw new \Exception();
			}
		} catch (\Exception $e) {
			throw $e;
		}
	}

	public function userWantsToBeRemembered(\authorization\model\UserCredentials $userCred,
											\authorization\model\LoginObserver $loginObserver) {
		try {
			$this->userDAL->insertTempUser($userCred, $this->ip);
			$loginObserver->loginOK($userCred->getTemporaryPassword(), true);
		} catch (\Exception $e) {
			
		}
	}

	public function isLoggedIn() {
		try {
			$user = $this->sessionAuthModel->load();
			if ($user->isSameSession())
				return true;
			throw new \Exception();
		} catch (\Exception $e) {
			return false;
		}
	}
	
	public function getLoggedInUser() {
		return $this->sessionAuthModel->load()->user;
	}
	
	public function doLogout() {
		$this->sessionAuthModel->remove();
	}
	
	private function setLoggedIn(\authorization\model\UserCredentials $userCred) {
		$this->sessionAuthModel->save(new \authorization\model\User($userCred));
	}
}