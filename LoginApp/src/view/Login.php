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
	 * @param \Model\User $user
	 */
	public function __construct(\Model\User $user) {
		$this->userModel = $user;
	}

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
	 * sets the username and password on the model
	 * @throws Exception if input does not exist
	 */
	public function loginInfo() {
		assert($this->userWantsToLogin());

		if (!isset($_POST[self::$usernamePOST]) || empty($_POST[self::$usernamePOST])) {
			$this->message = "Användarnamn saknas";
			throw new \Exception("Användarnamn saknas");
		} else if (!isset($_POST[self::$passwordPOST]) || empty($_POST[self::$passwordPOST])) {
			$this->userModel->setUsername($this->sanitize($_POST[self::$usernamePOST]));
			$this->message = "Lösenord saknas";
			throw new \Exception("Lösenord saknas");
		}

		$this->userModel->setUsername($this->sanitize($_POST[self::$usernamePOST]));
		$this->userModel->setPassword($this->sanitize($_POST[self::$passwordPOST]));
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
			<form method="post" action="?' . self::$loginGET . '">
				<fieldset>
					<legend>Login - Skriv in användarnamn och lösenord</legend>';

		if ($this->message) {
			$html .= "<p>$this->message</p>";
		}

		$html .= '
					<label for="' . self::$usernamePOST . '">Namn: </label>
					<input type="text" placeholder="Användarnamn" value="' . 
					$this->userModel->getUsername() .'" name="' . self::$usernamePOST . 
					'" id="' . self::$usernamePOST . '" autofocus>

					<label for="' . self::$passwordPOST . '">Lösenord: </label>
					<input type="password" placeholder="Lösenord" name="' . 
					self::$passwordPOST . '" id="' . self::$passwordPOST . '">

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

		$html = '<h2> ' . $this->userModel->getUsername() . ' är inloggad</h2>';

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