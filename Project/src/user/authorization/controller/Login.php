<?php

namespace authorization\controller;

require_once("./src/user/authorization/view/Login.php");
require_once("./src/user/authorization/model/Login.php");

class Login {

	/**
	 * @var \view\Login
	 */
	private $loginView;

	/**
	 * @var \view\Application
	 */
	private $applicationView;

	private $loginModel;

	public function __construct(\mysqli $mysqli, 
								\authorization\view\Login $loginView, 
								\application\view\Application $applicationView) {

		$this->loginModel = new \authorization\model\Login($mysqli);
			
		$this->loginView = $loginView;
		$this->applicationView = $applicationView;
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
					//@todo fix error handling, login ok but iwth remeber me error
				} catch (\Exception $e) {
					$this->loginView->LoginFailed();
				}
			}
		}
	}
}