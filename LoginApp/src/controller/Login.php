<?php

namespace controller;

require_once("./src/view/Login.php");
require_once("./src/model/User.php");
require_once("./src/model/SessionAuth.php");

class Login {

	/**
	 * @var \View\Login
	 */
	private $loginView;

	/**
	 * @var \model\User
	 */
	private $userModel;

	/**
	 * @var \model\SessionAuth
	 */
	private $sessionAuthModel;

	/**
	 * @var \model\UserDAL
	 */
	private $userDAL;

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
	 * @param \model\UserDAL $userDAL
	 */
	public function __construct(\model\UserDAL $userDAL) {
		$this->userDAL = $userDAL;
		$this->sessionAuthModel = new \model\SessionAuth();
			
		$this->loginView = new \view\Login();	
		
		try {
			$this->userModel = $this->sessionAuthModel->load();
		} catch(\Exception $e) {
			$userIP = $this->loginView->getIPAdress();
			$userAgent = $this->loginView->getUserAgent();
			$this->userModel = new \model\User($this->userDAL, $userIP, $userAgent);
		}
	}

	/**
	 * Checks what action the user has taken.
	 * @return string, returns a string of HTML
	 */
	public function userAction() {
		if($this->userModel->isUserLoggedIn() && !$this->loginView->userLogout() ) {		
			try {
				$this->controlSession();
				//Logged in
				return $this->loginView->getLoggedInHTML($this->userModel);
			} catch(\Exception $e) {
				return $this->loginView->getLoginForm();
			}	

		} else if (!$this->userModel->isUserLoggedIn() && $this->loginView->cookieLogin()) {
			//Cookie login
			return $this->userCookieLogin();

		} else if ($this->loginView->formLogin() && !$this->userModel->isUserLoggedIn()) {
			//Wants to log in
			return $this->userLogin();

		} else if ($this->userModel->isUserLoggedIn() && $this->loginView->userLogout() ) {
			try {
				$this->controlSession();
				//Log out
				return $this->userLogOut();
			} catch(\Exception $e) {
				return $this->loginView->getLoginForm();
			}	
				
		} else {
			//Default page, login form
			return $this->loginView->getLoginForm();
		}
	}

	/**
	 * Tries to log in the user by cookies
	 * @return String HTML
	 */
	private function userCookieLogin() {	
		try {
			$username = $this->loginView->getUsernameCookie();
			$token = $this->loginView->getTokenCookie();

			$this->userModel->loginByCookies($this->loginView, $username, $token);
			$this->sessionAuthModel->save($this->userModel);

			return $this->loginView->getLoggedInHTML($this->userModel);
		} catch(\Exception $e) {	
			$this->loginView->deleteCookies();

			return $this->loginView->getLoginForm();
		}
	}

	/**
	 * Tries to log in the user
	 * @return String HTML
	 */
	private function userLogin() {
		try {
			$username = $this->loginView->getFormUsername();
			$password = $this->loginView->getFormPassword();
			try {
				$this->userModel->login($this->loginView, $username, $password);
				$this->sessionAuthModel->save($this->userModel);

				if ($this->loginView->keepMeLoggedIn()) {
					$this->keepMeLoggedIn();
				}

				return $this->loginView->getLoggedInHTML($this->userModel);
			} catch(\Exception $e) {
				return $this->loginView->getLoginForm();
			}

		} catch(\Exception $e) {
			return $this->loginView->getLoginForm();
		}
	}

	/**
	 * Create cookies, save to database
	 */
	private function keepMeLoggedIn() {
		$username = $this->userModel->getUsername();
		$time = time() + 60;
		$this->userModel->keepMeLoggedIn($this->loginView, $username, $time);
		$tempId = $this->userModel->getTempId();
		$this->loginView->setCookies($username, $tempId, $time);
	}

	/**
	 * Tries to log out the user
	 * @return String HTML
	 */
	private function userLogOut() {
		try {
			$this->sessionAuthModel->remove();
			$this->loginView->deleteCookies();
			$this->userModel->logOut($this->loginView);

			return $this->loginView->getLoginForm();
		} catch(\Exception $e) {
			return $this->loginView->getLoggedInHTML();
		}
	}

	/**
	 * Check if session is in same browser and from same ip as last request
	 * @throws Exception If session do not match
	 */
	private function controlSession() {
		$userIP = $this->loginView->getIPAdress();
		$userAgent = $this->loginView->getUserAgent();
		if (!$this->userModel->compareSession($userIP, $userAgent)) {
			throw new \Exception('Sessions do not match');
		}
	}
}