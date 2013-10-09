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
	 * @param \model\UserDAL $userDAL
	 * @param \view\Login $loginView
	 * @param \model\User $userModel
	 */
	public function __construct(\model\UserDAL $userDAL, 
								\view\Login $loginView, 
								\model\User $userModel, 
								\model\SessionAuth $sessionAuthModel) {
		$this->userDAL = $userDAL;
		$this->sessionAuthModel = $sessionAuthModel;
		$this->userModel = $userModel;
			
		$this->loginView = $loginView;
	}

	/**
	 * Logged in user
	 * @return string HTML
	 */
	public function loggedInUser() {
		return $this->loginView->getLoggedInHTML($this->userModel);
	}

	/**
	 * Not logged in user
	 * @return string HTML
	 */
	public function notLoggedIn() {
		return $this->loginView->getLoginForm();
	}

	/**
	 * Tries to log in the user
	 * @return String HTML
	 */
	public function userLogin() {
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
	 * Tries to log out the user
	 * @return String HTML
	 */
	public function userLogOut() {
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
	 * Tries to log in the user by cookies
	 * @return String HTML
	 */
	public function userCookieLogin() {	
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
	 * Create cookies, save to database
	 */
	private function keepMeLoggedIn() {
		$username = $this->userModel->getUsername();
		$time = time() + 60;
		$this->userModel->keepMeLoggedIn($this->loginView, $username, $time);
		$tempId = $this->userModel->getTempId();
		$this->loginView->setCookies($username, $tempId, $time);
	}
}