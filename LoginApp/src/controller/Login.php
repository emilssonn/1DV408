<?php

namespace Controller;

require_once("./src/view/Login.php");
require_once("./src/model/User.php");
require_once("./src/model/UserDAL.php");
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
	 * @param \mysqli $dbCon
	 */
	public function __construct(\mysqli $dbCon) {
		$this->sessionAuthModel = new \model\SessionAuth();

		try {
			$this->userModel = $this->sessionAuthModel->load();
		} catch(\Exception $e) {
			$this->userModel = new \Model\User();
		}
		
		$this->loginView = new \view\Login();	
		$this->userDAL = new \Model\UserDAL($dbCon);
		$this->crypt = new \Model\Crypt();
	}

	/**
	 * Checks what action the user has taken.
	 * @return string, returns a string of HTML
	 */
	public function userAction() {
		if($this->userModel->isUserLoggedIn() && !$this->loginView->userWantsToLogout() ) {		
			//Logged in
			return $this->loginView->getLoggedInHTML($this->userModel);
		} else if (!$this->userModel->isUserLoggedIn() && $this->loginView->cookieLogin()) {
			//Cookie login
			return $this->userCookieLogin();
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

	private function userCookieLogin() {
			
		try {
			$username = $this->loginView->getUsernameCookie();
			$randString = $this->loginView->getPasswordCookie();
			$userIP = $this->loginView->getIPAdress();

			$this->userModel->loginByCookies($this->userDAL, $username, $randString, $userIP);
			$this->sessionAuthModel->login($this->userModel);

			return $this->loginView->getLoggedInHTML($this->userModel, "Inloggningen lyckades via cookies");
		} catch(\Exception $e) {	
			$this->loginView->deleteCookies();
			return $this->loginView->getLoginForm("Felaktig information i cookie");
		}
		
	}

	/**
	 * Tries to log in the user
	 * @return String, string of HTML
	 */
	private function userLogin() {
		try {
			$username = $this->loginView->getUsername();
			$password = $this->loginView->getPassword();
			try {
				$this->userModel->login($username, $password);
				$this->sessionAuthModel->login($this->userModel);
				if ($this->loginView->keepMeLoggedIn()) {
					$username = $this->userModel->getUsername();
					$randString = $this->crypt->crypt(time());
					$userIP = $this->loginView->getIPAdress();
					$time = time() + 60;
					$this->userDAL->insertTempUser($username, $randString, $time, $userIP);

					$this->loginView->setCookies($username, $randString, $time);
					return $this->loginView->getLoggedInHTML($this->userModel, "Inloggningen lyckades och vi kommer ihåg dig nästa gång");
				}
				return $this->loginView->getLoggedInHTML($this->userModel, "Inloggningen lyckades");
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
			$this->loginView->deleteCookies();
			$this->sessionAuthModel->logout();
			$this->userModel->logOut();
			return $this->loginView->getLoginForm("Du har nu loggat ut");
		} catch(\Exception $e) {
			return $this->loginView->getLoggedInHTML($e->getMessage());
		}
	}
}