<?php

namespace user\authorization\controller;

require_once("./src/user/authorization/view/Login.php");
require_once("./src/user/authorization/model/Login.php");
require_once("./src/common/controller/IController.php");

/**
 * @author Daniel Toll - https://github.com/dntoll
 * Changes by Peter Emilsson
 */
class Login implements \common\controller\IController {

	/**
	 * @var \user\login\view\Login
	 */
	private $loginView;

	/**
	 * @var \application\view\Navigation
	 */
	private $navigationView;

	/**
	 * @var \user\authorization\view\Login
	 */
	private $loginModel;

	public function __construct(\user\authorization\view\Login $loginView, 
								\application\view\Navigation $navigationView) {

		$this->loginModel = new \user\authorization\model\Login();
			
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

	public function run() {
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