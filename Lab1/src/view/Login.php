<?php

namespace View;

require_once("./src/model/User.php");

class Login {

	/**
	 * Name in HTML form and location in $_POST
	 * @var string
	 */
	private static $username = "LoginView::Username";

	/**
	 * Name in HTML form and location in $_POST
	 * @var string
	 */
	private static $password = "LoginView::Password";

	/**
	 * @var \Model\User
	 */
	private $user;

	/**
	 * username and password
	 * @var array
	 */
	private $userInfo = array();

	/**
	 * @var string
	 */
	private $message = null;

	/**
	 * @param \Model\User $user
	 */
	public function __construct(\Model\User $user) {
		$this->user = $user;
	}

	/**
	 * true if user wants to login
	 * @return bool
	 */
	public function userWantsToLogin() {
		return isset($_GET['login']);
	}

	/**
	 * true if user wants to logout
	 * @return bool
	 */
	public function userWantsToLogout() {
		return isset($_GET['logout']);
	}

	/**
	 * @return array, return array with username and password
	 * @throws Exception if input does not exist
	 */
	public function getLoginInfo() {
		assert($this->userWantsToLogin());

		if (!isset($_POST[self::$username]) || empty($_POST[self::$username])) {
			$this->message = "Användarnamn saknas";
			throw new \Exception("Användarnamn saknas");
		} else if (!isset($_POST[self::$password]) || empty($_POST[self::$password])) {
			$this->user->setUsername($this->sanitize($_POST[self::$username]));
			$this->message = "Lösenord saknas";
			throw new \Exception("Lösenord saknas");
		}

		$this->userInfo["username"] = $this->sanitize($_POST[self::$username]);
		$this->userInfo["password"] = $this->sanitize($_POST[self::$password]);

		return $this->userInfo;
	}

	/**
	 * @param  String $message
	 * @return HTML, returns string of HTML
	 */
	public function getLoginForm($message = null) {
		if ($message) {
			$this->message = $message;
		}

		$html =  '
			<h2>Ej inloggad</h2>
			<form method="post" action="?login">
				<fieldset>
					<legend>Login - Skriv in användarnamn och lösenord</legend>';

		if ($this->message) {
			$html .= "<p>$this->message</p>";
		}

		$html .= '
					<label for="' . self::$username . '">Namn: </label>
					<input type="text" placeholder="Användarnamn" value="' . $this->user->getUsername() .'" name="' . self::$username . '" id="' . self::$username . '" autofocus>
					<label for="' . self::$password . '">Lösenord: </label>
					<input type="password" placeholder="Lösenord" name="' . self::$password . '" id="' . self::$password . '">
					<button type="submit">Logga in</button>
				</fieldset>
			</form>';

		return $html;
	}

	/**
	 * @param  String $message
	 * @return HTML, returns string of HTML 
	 */
	public function getLoggedInHTML($message = null) {
		if ($message) {
			$this->message = $message;
		}

		$html = '
				<h2> ' . $this->user->getUsername() . ' är inloggad</h2>';

		if ($this->message) {
			$html .= "<p>$this->message</p>";
		}

		$html .= "<a href='?logout'>Logga ut</a>
				</form>";

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