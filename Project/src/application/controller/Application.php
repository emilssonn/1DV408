<?php

namespace application\controller;

require_once("./src/user/authorization/view/Login.php");
require_once("./src/application/view/Application.php");
require_once("./src/user/authorization/controller/Login.php");
require_once("./src/application/view/Navigation.php");
require_once("./src/user/register/view/Register.php");
require_once("./src/user/register/controller/Register.php");

class Application {

	/**
	 * @var \mysqli
	 */
	private $mysqli;

		/**
	 * @var \view\Login
	 */
	private $loginView;

	/**
	 * @var \controller\Login
	 */
	private $loginController;

	/**
	 * @var \view\Application
	 */
	private $applicationView;

	private $navigationView;

	/**
	 * Init
	 */
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;

		$this->navigationView = new \application\view\Navigation();
		$this->loginView = new \authorization\view\Login($this->navigationView);	
		$this->applicationView = new \application\view\Application($this->navigationView, $this->loginView);	

		$this->loginController = new \authorization\controller\Login(
														$mysqli,
														$this->loginView, 
														$this->applicationView);

		
	}

	/**
	 * @return string HTML
	 */
	public function runApplication() {
		$this->loginController->doToggleLogin();
	
		if ($this->loginController->isLoggedIn()) {
			$loggedInUserCredentials = $this->loginController->getLoggedInUser();
			return $this->applicationView->getLoggedInPage($loggedInUserCredentials);	
		} else if ($this->navigationView->wantsToRegister()) {
			$registerView = new \register\view\Register($this->navigationView);
			$registerController = new \register\controller\Register($registerView, $this->loginView, $this->mysqli);
			$registerController->doToggleRegister();
			if ($registerController->wasRegSuccessfull()) {
				return $this->applicationView->getLoggedOutPage();
			} else {
				return $this->applicationView->getRegisterPage($registerView);
			}
		} else {
			return $this->applicationView->getLoggedOutPage();
		}
	}
}