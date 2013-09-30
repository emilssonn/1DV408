<?php

namespace View;

require_once("./src/model/User.php");

class Login {

	/**
	 * Name in HTML form and location in $_POST
	 * @var string
	 */
	private static $usernamePOST = "View::Login::Username";

	/**
	 * Name in HTML form and location in $_POST
	 * @var string
	 */
	private static $passwordPOST = "View::Login::Password";

	/**
	 * Name in HTML form and location in $_POST
	 * @var string
	 */
	private static $cookieLoginPOST = "View::Login::CookieLogin";

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

	/**
	 * @var \Model\User
	 */
	private $userModel;

	/**
	 * @var string
	 */
	private $message = null;

	/**
	 * @var string
	 */
	private $username;

	/**
	 * true if user wants to login
	 * @return bool
	 */
	public function userWantsToLogin() {
		return isset($_GET[self::$loginGET]);
	}

	/**
	 * true if user wants to logout
	 * @return bool
	 */
	public function userWantsToLogout() {
		return isset($_GET[self::$logoutGET]);
	}

	/**
	 * [getPassword description]
	 * @return string
	 */
	public function getPassword() {
		assert($this->userWantsToLogin());

		if (!isset($_POST[self::$passwordPOST]) || empty($_POST[self::$passwordPOST])) {
			$this->message = "Lösenord saknas";
			throw new \Exception("Lösenord saknas");
		}

		return $this->sanitize($_POST[self::$passwordPOST]);
	}

	/**
	 * [getUsername description]
	 * @return stirng
	 */
	public function getUsername() {
		assert($this->userWantsToLogin());

		if (!isset($_POST[self::$usernamePOST]) || empty($_POST[self::$usernamePOST])) {
			$this->username = $this->sanitize($_POST[self::$usernamePOST]);
			$this->message = "Användarnamn saknas";
			throw new \Exception("Användarnamn saknas");
		}

		$this->username = $this->sanitize($_POST[self::$usernamePOST]);

		return $this->sanitize($_POST[self::$usernamePOST]);
	}

	/**
	 * [cookieLogin description]
	 * @return bool [description]
	 */
	public function cookieLogin() {
		return isset($_COOKIE[self::$usernamePOST]) && isset($_COOKIE[self::$passwordPOST]);
	}

	/**
	 * [keepMeLoggedIn description]
	 * @return bool [description]
	 */
	public function keepMeLoggedIn() {
		return isset($_POST[self::$cookieLoginPOST]);
	}

	/**
	 * [setCookies description]
	 * @param string $username 
	 * @param string $randString [description]
	 * @param string $time     [description]
	 */
	public function setCookies($username, $randString, $time) {
		setcookie(self::$usernamePOST, $username, $time);
		setcookie(self::$passwordPOST, $randString, $time);
	}

	/**
	 * @return [type] [description]
	 */
	public function deleteCookies() {
		setcookie(self::$usernamePOST, "", time() - 3600);
		setcookie(self::$passwordPOST, "", time() - 3600);
	}

	/**
	 * [getIPAdress description]
	 * @return string [description]
	 */
	public function getIPAdress() {
		return $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * [getPasswordCookie description]
	 * @return string [description]
	 */
	public function getPasswordCookie() {
		assert($this->cookieLogin());

		return $this->sanitize($_COOKIE[self::$passwordPOST]);
	}

	/**
	 * [getUsernameCookie description]
	 * @return string [description]
	 */
	public function getUsernameCookie() {
		assert($this->cookieLogin());

		return $this->sanitize($_COOKIE[self::$usernamePOST]);
	}

	/**
	 * @param  String $message
	 * @return HTML, returns string of HTML
	 */
	public function getLoginForm($message = null) {
		if ($message) {
			$this->message = $message;
		}

		$html = $this->getLoginFormHead();

		if ($this->message) {
			$html .= "<p>$this->message</p>";
		}

		$html .= $this->getLoginFormBody();

		return $html;
	}

	private function getLoginFormHead() {
		return '
			<h2>Ej inloggad</h2>
			<form method="post" action="?' . self::$loginGET . '">
				<fieldset>
					<legend>Login - Skriv in användarnamn och lösenord</legend>';
	}

	//@TODO kolla om check rutan ska vara i fylld
	private function getLoginFormBody() {
		return '
					<label for="' . self::$usernamePOST . '">Namn: </label>
					<input type="text" placeholder="Användarnamn" value="' . 
					$this->username .'" name="' . self::$usernamePOST . 
					'" id="' . self::$usernamePOST . '" autofocus>

					<label for="' . self::$passwordPOST . '">Lösenord: </label>
					<input type="password" placeholder="Lösenord" name="' . 
					self::$passwordPOST . '" id="' . self::$passwordPOST . '">

					<label for="' . self::$cookieLoginPOST . '" >Håll mig inloggad  :</label>
					<input type="checkbox" name="' . self::$cookieLoginPOST . '" id="' . 
					self::$cookieLoginPOST . '" />

					<button type="submit">Logga in</button>
				</fieldset>
			</form>';
	}

	/**
	 * @param \Model\User $user
	 * @param  String $message
	 * @return HTML, returns string of HTML 
	 */
	public function getLoggedInHTML(\Model\User $user, $message = null) {
		if ($message) {
			$this->message = $message;
		}

		$html = '<h2> ' . $user->getUsername() . ' är inloggad</h2>';

		if ($this->message) {
			$html .= "<p>$this->message</p>";
		}

		$html .= '<a href="?' . self::$logoutGET . '">Logga ut</a>';

		return $html;
	}

	/**
	 * Source: https://github.com/dntoll/1DV408ExamplesHT2013/blob/master/BookStoreSaveData/BookView.php
	 * @param String input
	 * @return String input - tags - trim
	 */
	private function sanitize($input) {
		$temp = trim($input);
		return filter_var($temp, FILTER_SANITIZE_STRING);
	}
}