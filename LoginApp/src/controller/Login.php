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
	private $userModel;

	/**
	 * @var \Model\SessionAuth
	 */
	private $sessionAuthModel;

	/**
	 * @return String page title
	 */
	public function getPageTitle() {
		if ($this->userModel->isUserLoggedIn()) {
			return "Laboration: Inloggad";
		} else {
			return "Laboration: Ej inloggad";
		}	
	}

	/**
	 * @param \Model\User 			$userModel      
	 * @param \Model\SessionAuth 	sessionAuthModel
	 */
	public function __construct(\Model\User $userModel, 
								\Model\SessionAuth $sessionAuthModel) {
		$this->userModel = $userModel;
		$this->loginView = new \view\Login($this->userModel);	
		$this->sessionAuthModel = $sessionAuthModel;
	}

	/**
	 * Checks what action the user has taken.
	 * @return string, returns a string of HTML
	 */
	public function userAction() {
		if($this->userModel->isUserLoggedIn() && !$this->loginView->userWantsToLogout() ) {		
			//Logged in
			return $this->loginView->getLoggedInHTML();
		} else if ($this->loginView->userWantsToLogin() && !$this->userModel->isUserLoggedIn()) {
			//Wants to log in
			return $this->userLogin();
		} else if ($this->userModel->isUserLoggedIn() && $this->loginView->userWantsToLogout() ) {
			//Log out
			return $this->userLogOut();
			
		} else {
			//Default page, login form
			return $this->loginView->getLoginForm();
		}
	}

	/**
	 * Tries to log in the user
	 * @return String, string of HTML
	 */
	private function userLogin() {
		try {
			$this->loginView->loginInfo();
			try {
				$this->userModel->login();
				$this->sessionAuthModel->login($this->userModel);
				return $this->loginView->getLoggedInHTML("Inloggningen lyckades");
			} catch(\Exception $e) {	
				return $this->loginView->getLoginForm($e->getMessage());
			}

		} catch(\Exception $e) {
			return $this->loginView->getLoginForm();
		}
	}

	/**
	 * Tries to log out the user
	 * @return String, string of HTML
	 */
	private function userLogOut() {
		try {
			$this->sessionAuthModel->logout();
			$this->userModel->logOut();
			return $this->loginView->getLoginForm("Du har nu loggat ut");
		} catch(\Exception $e) {
			return $this->loginView->getLoggedInHTML($e->getMessage());
		}
	}
}