<?php

namespace Controller;

require_once("./src/view/Login.php");
require_once("./src/model/User.php");
require_once("./src/model/Crypt.php");
require_once("./src/model/SessionAuth.php");

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
	 * @var \Model\UserDAL
	 */
	private $userDAL;

	/**
	 * @var \Model\Crypt
	 */
	private $crypt;

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
	 * @param \Model\UserDAL $userDAL
	 */
	public function __construct(\Model\UserDAL $userDAL) {
		$this->sessionAuthModel = new \model\SessionAuth();
		$this->userDAL = $userDAL;
		$this->loginView = new \view\Login();	
		$this->crypt = new \Model\Crypt();

		try {
			$this->userModel = $this->sessionAuthModel->load();
		} catch(\Exception $e) {
			$userIP = $this->loginView->getIPAdress();
			$userAgent = $this->loginView->getUserAgent();
			$this->userModel = new \Model\User($userIP, $userAgent);
		}
	}

	/**
	 * Checks what action the user has taken.
	 * @return string, returns a string of HTML
	 */
	public function userAction() {
		if($this->userModel->isUserLoggedIn() && !$this->loginView->userWantsToLogout() ) {		
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
		} else if ($this->loginView->userWantsToLogin() && !$this->userModel->isUserLoggedIn()) {
			//Wants to log in
			return $this->userLogin();
		} else if ($this->userModel->isUserLoggedIn() && $this->loginView->userWantsToLogout() ) {
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
			$randString = $this->loginView->getTokenCookie();

			$this->userModel->loginByCookies($this->userDAL, $username, $randString);
			$this->sessionAuthModel->login($this->userModel);
			$state = $this->userModel->getState();
			$this->loginView->setMessage($state);
			return $this->loginView->getLoggedInHTML($this->userModel);
		} catch(\Exception $e) {	
			$this->loginView->deleteCookies();
			$state = $this->userModel->getState();
			$this->loginView->setMessage($state);
			return $this->loginView->getLoginForm();
		}
	}

	/**
	 * Tries to log in the user
	 * @return String HTML
	 */
	private function userLogin() {
		try {
			$username = $this->loginView->getUsername();
			$password = $this->loginView->getPassword();
			try {
				$this->userModel->login($username, $password);
				$this->sessionAuthModel->login($this->userModel);

				if ($this->loginView->keepMeLoggedIn()) {
					$this->keepMeLoggedIn();
				}

				$state = $this->userModel->getState();
				$this->loginView->setMessage($state);
				return $this->loginView->getLoggedInHTML($this->userModel);
			} catch(\Exception $e) {
				$state = $this->userModel->getState();
				$this->loginView->setMessage($state);	
				return $this->loginView->getLoginForm();
			}

		} catch(\Exception $e) {
			$state = $this->userModel->getState();
			$this->loginView->setMessage($state);
			return $this->loginView->getLoginForm();
		}
	}

	/**
	 * Create cookies, save to database
	 */
	private function keepMeLoggedIn() {
		$username = $this->userModel->getUsername();
		$tempId = $this->crypt->crypt(time());
		$time = time() + 60;
		$this->userModel->keepMeLoggedIn($this->userDAL, $username, $tempId, $time);
		$this->loginView->setCookies($username, $tempId, $time);
	}

	/**
	 * Tries to log out the user
	 * @return String HTML
	 */
	private function userLogOut() {
		try {
			$this->sessionAuthModel->logout();
			$this->loginView->deleteCookies();
			$this->userModel->logOut();
			$state = $this->userModel->getState();
			$this->loginView->setMessage($state);
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
			throw new \Exception();
		}
	}
}