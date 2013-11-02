<?php

namespace user\authorization\model;

require_once("./src/user/authorization/model/SessionAuth.php");
require_once("./src/user/model/UserDAL.php");
require_once("./src/user/model/UserCredentials.php");

/**
 * @author Daniel Toll - https://github.com/dntoll
 * Changes by Peter Emilsson
 */
class Login {
	
	/**
	 * @var \user\authorization\model\SessionAuth
	 */
	private $sessionAuthModel;

	/**
	 * @var \user\model\UserDAL
	 */
	private $userDAL;

	/**
	 * @var string
	 */
	private $ip;
	
	public function __construct() {
		$this->sessionAuthModel = new \user\authorization\model\SessionAuth();
		$this->userDAL = new \user\model\UserDAL();
		$this->ip = $_SERVER["REMOTE_ADDR"];
	}
	
	/**
	 * @param  \user\model\UserCredentials             $userCred      
	 * @param  \user\authorization\model\LoginObserver $loginObserver 
	 * @return \user\model\UserCredentials
	 * @throws \Exception                                           
	 */
	public function doLogin(\user\model\UserCredentials $userCred, 
							\user\authorization\model\LoginObserver $loginObserver) {

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

	/**
	 * @param  \user\model\UserCredentials            $userCred     
	 * @param  \user\authorization\model\LoginObserver $loginObserver 
	 */
	public function userWantsToBeRemembered(\user\model\UserCredentials $userCred,
											\user\authorization\model\LoginObserver $loginObserver) {
		try {
			$this->userDAL->insertTempUser($userCred, $this->ip);
			$loginObserver->loginOK($userCred->getTemporaryPassword(), true);
		} catch (\Exception $e) {
			
		}
	}

	/**
	 * @return boolean
	 */
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
	
	/**
	 * @return \user\authorization\model\User
	 */
	public function getLoggedInUser() {
		return $this->sessionAuthModel->load()->user;
	}
	
	public function doLogout() {
		$this->sessionAuthModel->remove();
	}
	
	/**
	 * @param \user\model\UserCredentials $userCred 
	 */
	private function setLoggedIn(\user\model\UserCredentials $userCred) {
		$this->sessionAuthModel->save(new \user\authorization\model\User($userCred));
	}
}