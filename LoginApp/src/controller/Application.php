<?php

namespace controller;

require_once("./src/model/UserDAL.php");
require_once("./src/model/User.php");
require_once("./src/model/SessionAuth.php");
require_once("./src/view/Login.php");
require_once("./src/view/Application.php");
require_once("./src/controller/Login.php");

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
	 * @var \model\UserDAL
	 */
	private $userDAL;

	/**
	 * @var \model\User
	 */
	private $userModel;

	/**
	 * @var \model\SessionAuth
	 */
	private $sessionAuthModel;

	/**
	 * @var \view\Application
	 */
	private $applicationView;

	/**
	 * Init
	 */
	public function __construct($mysqli) {
		$this->mysqli = $mysqli;

		$this->userDAL = new \model\UserDAL($this->mysqli);
		$this->sessionAuthModel = new \model\SessionAuth();

		$this->applicationView = new \view\Application();	
		$this->loginView = new \view\Login($this->applicationView);	

		try {
			$this->userModel = $this->sessionAuthModel->load();
		} catch(\Exception $e) {
			$userIP = $this->applicationView->getIPAdress();
			$userAgent = $this->applicationView->getUserAgent();
			$this->userModel = new \model\User($this->userDAL, $userIP, $userAgent);
		}

		$this->loginController = new \controller\Login($this->userDAL, 
														$this->loginView, 
														$this->userModel,
														$this->sessionAuthModel,
														$this->applicationView);
	}

	/**
	 * @return string HTML
	 */
	public function runApplication() {
		$html;
		if($this->userModel->isUserLoggedIn() && !$this->applicationView->userLogout() ) {		
			try {
				$this->controlSession();
				//Continue with if statements for a bigger application
				//Logged in
				$html = $this->loginController->loggedInUser();
			} catch(\Exception $e) {
				$html = $this->loginController->notLoggedIn();
			}	
		} else if (!$this->userModel->isUserLoggedIn() && $this->loginView->cookieLogin()) {
			//Cookie login
			//@todo, how to move this to application view
			$html = $this->loginController->userCookieLogin();
		} else if ($this->applicationView->formLogin() && !$this->userModel->isUserLoggedIn()) {
			//Wants to log in
			$html = $this->loginController->userLogin();
		} else if ($this->userModel->isUserLoggedIn() && $this->applicationView->userLogout() ) {
			try {
				$this->controlSession();
				//Log out
				$html = $this->loginController->userLogOut();
			} catch(\Exception $e) {
				$html = $this->loginController->notLoggedIn();
			}		
		} else {
			//Default page, login form
			$html = $this->loginController->notLoggedIn();
		}
		return $this->applicationView->getPageHTML($html);
	}

	/**
	 * Control if the session is valid, ip and user agent match
	 * @return bool
	 */
	private function controlSession() {
		$userIP = $this->applicationView->getIPAdress();
		$userAgent = $this->applicationView->getUserAgent();
		if (!$this->userModel->compareSession($userIP, $userAgent)) {
			throw new \Exception('Sessions do not match');
		}
		return true;
	}
}