<?php

namespace Controller;

require_once("/../view/Login.php");
require_once("/../model/User.php");

/**
 * 
 */
class Login {

	/**
	 * @var \View\Login
	 */
	private $loginView;

	/**
	 * @var \Model\User
	 */
	private $user;

	/**
	 * @var \Model\UserLogin
	 */
	private $userLogin;

	/**
	 * @param \Model\User 		$user      
	 * @param \Model\UserLogin 	$userLogin 
	 */
	public function __construct(\Model\User $user, \Model\UserLogin $userLogin) {
		$this->user = $user;
		$this->loginView = new \view\Login($this->user);	
		$this->userLogin = $userLogin;
	}

	/**
	 * Checks what action the user has taken.
	 * @return HTML returns a string of HTML
	 */
	public function checkUser() {
		if($this->user->isUserLoggedIn() && !$this->loginView->userWantsToLogout() ) {
			
			return $this->loginView->getLoggedInHTML();
		}
		else if ($this->loginView->userWantsToLogin() && !$this->user->isUserLoggedIn()) {
			
			try {
				$userInfo = $this->loginView->getLoginInfo();
				if ($this->user->login($userInfo["username"], $userInfo["password"])) {
					
					$this->userLogin->saveUser($this->user);
					return $this->loginView->getLoggedInHTML("Inloggningen lyckades");
				} else {
					
					return $this->loginView->getLoginForm("Felaktigt användarnamn och/eller lösenord");
				}

			} catch(\Exception $e) {
				
				return $this->loginView->getLoginForm($e->getMessage());
			}
		} else if ($this->user->isUserLoggedIn() && $this->loginView->userWantsToLogout() ) {
			
			$this->userLogin->logout();
			$this->user->logout();
			return $this->loginView->getLoginForm("Utloggningen lyckades");
		} else {
			
			return $this->loginView->getLoginForm();
		}
	}
}