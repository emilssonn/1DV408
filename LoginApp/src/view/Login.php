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
	 * true if cookies exists to login
	 * @return bool
	 */
	public function cookieLogin() {
		return isset($_COOKIE[self::$usernamePOST]) && isset($_COOKIE[self::$passwordPOST]);
	}

	/**
	 * true if user wants to stay logged in past this session
	 * @return bool
	 */
	public function keepMeLoggedIn() {
		return isset($_POST[self::$cookieLoginPOST]);
	}

	/**
	 * Set succes/error message
	 * @param CONST $state
	 */
	public function setMessage($state) {
		switch ($state) {
			case \Model\User::successLogin:
				$this->message = "Inloggningen lyckades";
				break;

			case \Model\User::successCookieLogin:
				$this->message = "Inloggningen lyckades via cookies";
				break;
			
			case \Model\User::failedCookieLogin:
				$this->message = "Felaktig information i cookie";
				break;

			case \Model\User::wrongUsernamePassword:
				$this->message = "Felaktigt användarnamn och/eller lösenord";
				break;

			case \Model\User::successLogout:
				$this->message = "Du har nu loggat ut";
				break;

			case \Model\User::successLoginKeep:
				$this->message = "Inloggningen lyckades och vi kommer ihåg dig nästa gång";
				break;
		}
	}

	/**
	 * @return string
	 * @throws Exception If no password
	 */
	public function getPassword() {
		assert($this->userWantsToLogin());

		if (!isset($_POST[self::$passwordPOST]) || empty($_POST[self::$passwordPOST])) {
			$this->username = $this->sanitize($_POST[self::$usernamePOST]);
			$this->message = "Lösenord saknas";
			throw new \Exception();
		}

		return $this->sanitize($_POST[self::$passwordPOST]);
	}

	/**
	 * @return stirng
	 * @throws Exception If no username
	 */
	public function getUsername() {
		assert($this->userWantsToLogin());

		if (!isset($_POST[self::$usernamePOST]) || empty($_POST[self::$usernamePOST])) {
			$this->message = "Användarnamn saknas";
			throw new \Exception();
		}

		$this->username = $this->sanitize($_POST[self::$usernamePOST]);

		return $this->sanitize($_POST[self::$usernamePOST]);
	}

	/**
	 * @param string $username 
	 * @param string $tempId
	 * @param int $time 
	 */
	public function setCookies($username, $tempId, $time) {
		setcookie(self::$usernamePOST, $username, $time, "", "", false , true);
		setcookie(self::$passwordPOST, $tempId, $time, "", "", false , true);
	}

	/**
	 * Delete the cookies
	 */
	public function deleteCookies() {
		setcookie(self::$usernamePOST, "", time() - 3600);
		setcookie(self::$passwordPOST, "", time() - 3600);
	}

	/**
	 * @return string
	 */
	public function getTokenCookie() {
		assert($this->cookieLogin());

		return $this->sanitize($_COOKIE[self::$passwordPOST]);
	}

	/**
	 * @return string
	 */
	public function getUsernameCookie() {
		assert($this->cookieLogin());

		return $this->sanitize($_COOKIE[self::$usernamePOST]);
	}

	/**
	 * @return string
	 */
	public function getIPAdress() {
		return $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * @return string
	 */
	public function getUserAgent() {
		return $_SERVER['HTTP_USER_AGENT'];
	}

	/**
	 * @param \Model\User $user
	 * @return HTML, returns string of HTML 
	 */
	public function getLoggedInHTML(\Model\User $user) {
		$html = '<h2> ' . $user->getUsername() . ' är inloggad</h2>';

		if ($this->message) {
			$html .= "<p>$this->message</p>";
		}

		$html .= '<a href="?' . self::$logoutGET . '">Logga ut</a>';

		return $html;
	}

	/**
	 * @return HTML, returns string of HTML
	 */
	public function getLoginForm() {
		$html = $this->getLoginFormHead();

		if ($this->message) {
			$html .= "<p>$this->message</p>";
		}

		$html .= $this->getLoginFormBody();

		return $html;
	}

	/**
	 * @return string HTML form head
	 */
	private function getLoginFormHead() {
		return '
			<h2>Ej inloggad</h2>
			<form method="post" action="?' . self::$loginGET . '">
				<fieldset>
					<legend>Login - Skriv in användarnamn och lösenord</legend>';
	}

	/**
	 * @return string HTML form body
	 */
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
	 * Source: https://github.com/dntoll/1DV408ExamplesHT2013/blob/master/BookStoreSaveData/BookView.php
	 * @param String input
	 * @return String input - tags - trim
	 */
	private function sanitize($input) {
		$temp = trim($input);
		return filter_var($temp, FILTER_SANITIZE_STRING);
	}
}