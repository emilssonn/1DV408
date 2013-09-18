<?php

namespace Controller;

require_once("./src/view/Login.php");
require_once("./src/model/User.php");

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

	public function getPageTitle() {
		if ($this->user->isUserLoggedIn()) {
			return "Laboration: Inloggad";
		} else {
			return "Laboration: Ej inloggad";
		}	
	}

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
	 * @return string, returns a string of HTML
	 */
	public function checkUser() {
		if($this->user->isUserLoggedIn() && !$this->loginView->userWantsToLogout() ) {		
			//Logged in
			return $this->loginView->getLoggedInHTML();
		} else if ($this->loginView->userWantsToLogin() && !$this->user->isUserLoggedIn()) {
			//Wants to log in
			try {
				$userInfo = $this->loginView->getLoginInfo();
				try {
					$this->user->login($userInfo["username"], $userInfo["password"]);
					$this->userLogin->login($this->user);
					return $this->loginView->getLoggedInHTML("Inloggningen lyckades");
				} catch(\Exception $e) {	
					return $this->loginView->getLoginForm($e->getMessage());
				}

			} catch(\Exception $e) {			
				return $this->loginView->getLoginForm();
			}
		} else if ($this->user->isUserLoggedIn() && $this->loginView->userWantsToLogout() ) {
			//Log out
			$this->userLogin->logout();
			$this->user->logOut();
			return $this->loginView->getLoginForm("Du har nu loggat ut");
		} else {
			//Default page, login form
			return $this->loginView->getLoginForm();
		}
	}
}