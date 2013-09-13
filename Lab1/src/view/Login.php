<?php

namespace View;

require_once("/../model/User.php");

class Login {

	/**
	 * @var \Model\User
	 */
	private $user;

	/**
	 * @var array, username and password
	 */
	private $userInfo = array();

	/**
	 * @param \Model\User $user
	 */
	public function __construct(\Model\User $user) {
		$this->user = $user;
	}

	/**
	 * @return bool, true if user wants to login
	 */
	public function userWantsToLogin() {
		return isset($_GET['login']);
	}

	/**
	 * @return bool, true if user wants to logout
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

		if (!isset($_POST["username"]) || empty($_POST["username"])) {
			throw new \Exception("Användarnamn saknas");
		} else if (!isset($_POST["password"]) || empty($_POST["password"])) {
			$this->user->setUsername($this->getCleanInput("username"));
			throw new \Exception("Lösenord saknas");
		}

		$this->userInfo["username"] = $this->getCleanInput("username");
		$this->userInfo["password"] = $this->getCleanInput("password");

		return $this->userInfo;
	}

	/**
	 * @param  String $message
	 * @return HTML, returns string of HTML
	 */
	public function getLoginForm($message = null) {
		$html =  '
			<h3>Ej inloggad</h3>
			<form method="post" action="?login">
				<fieldset>
					<legend>Skriv in användarnamn och lösenord</legend>';

		if ($message) {
			$html .= "<p>$message</p>";
		}

		$html .= '
				<input type="text" placeholder="Användarnamn" value="' . $this->user->getUsername() .'" name="username" autofocus>
				<input type="password" placeholder="Lösenord" name="password">
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
		$html = '
				<h3> ' . $this->user->getUsername() . ' är inloggad</h3>';

		if ($message) {
			$html .= "<p>$message</p>";
		}

		$html .= "<a href='?logout'>Logga ut</a>
				</form>";

		return $html;
	}

	/**
	 * Source: https://github.com/dntoll/1DV408ExamplesHT2013/blob/master/BookStoreSaveData/BookView.php
	 * @param String input
	 * @return String input - tags - trim
	 * @throws Exception if something is wrong or input does not exist
	 */
	private function getCleanInput($inputName) {
		if (isset($_POST[$inputName]) == false) {
			return "";
		}

		return $this->sanitize($_POST[$inputName]);
	}

	/**
	 * Source: https://github.com/dntoll/1DV408ExamplesHT2013/blob/master/BookStoreSaveData/BookView.php
	 * @param String input
	 * @return String input - tags - trim
	 */
	private function sanitize($input) {
		$temp = trim($input);
		return filter_var($temp, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	}
}