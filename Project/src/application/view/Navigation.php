<?php

namespace application\view;

class Navigation {
	
	/**
	 * Name in URL for login
	 * @var string
	 */
	private static $loginGET = "login";

	/**
	 * Name in URL for logout
	 * @var string
	 */
	private static $logoutGET = "logout";

	private static $registerGET = "register";



	/**
	 * @return string
	 */
	public function getLogin() {
		return self::$loginGET;
	}

	/**
	 * @return string
	 */
	public function getLogOut() {
		return self::$logoutGET;
	}

	public function getRegister() {
		return self::$registerGET;
	}

	public function loginWithReturnUrl() {
		header("Location: ?login&ref=" . $_SERVER["QUERY_STRING"]);
	}

	/**
	 * @return String HTML
	 */
	public function getLogoutButton() {
		return "<a href='?" . $this->getLogOut() . "'>Logga ut</a>";
	}

	/**
	 * @return string HTML
	 */
	public function getRegisterLink() {
		$register = $this->getRegister();
		return "<a href='?$register'>Registrera ny användare</a>";
	}

	/**
	 * @return boolean
	 */
	public function wantsToRegister() {
		return isset($_GET[$this->getRegister()]);
	}

}