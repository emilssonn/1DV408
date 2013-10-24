<?php

namespace authorization\controller;

require_once("./src/user/authorization/view/Login.php");
require_once("./src/user/authorization/model/Login.php");

class Login {

	/**
	 * @var \view\Login
	 */
	private $loginView;

	private $navigationView;

	private $loginModel;

	public function __construct(\authorization\view\Login $loginView, 
								\application\view\Navigation $navigationView) {

		$this->loginModel = new \authorization\model\Login();
			
		$this->loginView = $loginView;
		$this->navigationView = $navigationView;
	}

	/**
	 * Logged in user
	 * @return string HTML
	 */
	public function isLoggedIn() {
		return $this->loginModel->isLoggedIn();
	}

	/** 
	 * Facade
	 * @return \login\model\UserCredentials
	 */
	public function getLoggedInUser() {
		return $this->loginModel->getLoggedInUser();
	}

	/**
	 * Handle input
	 * Make sure to log statechanges
	 *
	 * note this has no output, output goes through views that are called seperately
	 */
	public function doToggleLogin() {
		if ($this->loginModel->isLoggedIn()) {
			if ($this->loginView->isLoggingOut() ) {
				$this->loginModel->doLogout();
				$this->loginView->doLogout();
			}
		} else {
			if ($this->loginView->isLoggingIn() ) {
				try {
					$credentials = $this->loginView->getUserCredentials();
					$credentials = $this->loginModel->doLogin($credentials, $this->loginView);
					if ($this->loginView->userWantsToBeRemembered())
						$this->loginModel->userWantsToBeRemembered($credentials, $this->loginView);
					$this->navigationView->goToHome();
				} catch (\Exception $e) {
					$this->loginView->LoginFailed();
				}
			}
		}
	}
}