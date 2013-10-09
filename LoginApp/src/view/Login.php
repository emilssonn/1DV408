<?php

namespace view;

require_once("./src/model/User.php");
require_once("./src/model/LoginObserver.php");

class Login implements \model\LoginObserver {

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
	 * @var \model\User
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
	 * @var \view\Application
	 */
	private $applicationView;

	/**
	 * @param \view\Application $applicationView
	 */
	public function __construct(\view\Application $applicationView) {
		$this->applicationView = $applicationView;
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
	 * @return string
	 * @throws Exception If no username or empty
	 */
	public function getFormUsername() {
		assert($this->applicationView->formLogin());

		if (!isset($_POST[self::$usernamePOST]) || empty($_POST[self::$usernamePOST])) {
			$this->message = "Användarnamn saknas";
			throw new \Exception('No username entered');
		} else {
			return $this->sanitize($_POST[self::$usernamePOST]);
		}
	}

	/**
	 * @return string
	 * @throws Exception If no password or empty
	 */
	public function getFormPassword() {
		assert($this->applicationView->formLogin());

		if (!isset($_POST[self::$passwordPOST]) || empty($_POST[self::$passwordPOST])) {
			$this->username = $this->sanitize($_POST[self::$usernamePOST]);
			$this->message = "Lösenord saknas";
			throw new \Exception('No password entered');
		} else {
			return $this->sanitize($_POST[self::$passwordPOST]);
		}
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
	 * @param \model\User $user
	 * @return HTML, returns string of HTML 
	 */
	public function getLoggedInHTML(\model\User $user) {
		$html = '<h2> ' . $user->getUsername() . ' är inloggad</h2>';

		if ($this->message) {
			$html .= "<p>$this->message</p>";
		}

		$html .= '<a href="?' . $this->applicationView->getLogOutLink() . '">Logga ut</a>';

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
			<form method="post" action="?' . $this->applicationView->getLoginLink() . '">
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
	 * @param String input
	 * @return String input
	 */
	private function sanitize($input) {
		$temp = trim($input);
		return htmlentities($temp);
	}

	public function okFormLogin() {
		$this->message = "Inloggningen lyckades";
	}

	public function okCookieLogin() {
		$this->message = "Inloggningen lyckades via cookies";
	}

	public function okLogOut() {
		$this->message = "Du har nu loggat ut";
	}

	public function okKeepMeLoggedIn() {
		$this->message = "Inloggningen lyckades och vi kommer ihåg dig nästa gång";
	}

	public function failedCookieLogin() {
		$this->message = "Felaktig information i cookie";
	}

	public function wrongUserCredentials() {
		$this->message = "Felaktigt användarnamn och/eller lösenord";
	}
}