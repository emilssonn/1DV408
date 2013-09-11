<?php

namespace View;

require_once("/../model/UserModel.php");

class LoginView {

	/**
	 * @var string
	 */
	private static $loginButton = "login";

	/**
	 * [userWantsToLogin description]
	 * @return bool
	 */
	public function userWantsToLogin() {
		return isset($_GET['login']);
	}

	/**
	 * [userWantsToLogout description]
	 * @return [type] [description]
	 */
	public function userWantsToLogout() {
		return isset($_GET['logout']);
	}

	/**
	 * [getLoginInfo description]
	 * @return [type] [description]
	 */
	public function getLoginInfo() {
		assert($this->userWantsToLogin());

		$userInfo = array();

		if (!isset($_POST["username"]) || empty($_POST["username"])) {
			throw new \Exception("Användarnamn saknas");
		} else if (!isset($_POST["password"]) || empty($_POST["password"])) {
			throw new \Exception("Lösenord saknas");
		}

		$userInfo["username"] = $this->getCleanInput("username");
		$userInfo["password"] = $_POST["password"];

		return $userInfo;
	}

	/**
	 * [getLoginForm description]
	 * @param  ModelUser $userModel [description]
	 * @param  [type]    $message   [description]
	 * @return [type]               [description]
	 */
	public function getLoginForm(\Model\User $userModel, $message = null) {
		$html =  '
			<form class="form-signin" method="post" action="?login">
				<h2 class="form-signin-heading">Laborationskod xx22aa</h2>';

		if ($message) {
			$html .= "<p>$message</p>";
		}

		$html .= '
				<input type="text" class="form-control" placeholder="Användarnamn" value="' . $userModel->getUsername() .'" name="username" autofocus>
				<input type="password" class="form-control" placeholder="Lösenord" name="password">
				<button class="btn btn-lg btn-primary btn-block" type="submit">Logga in</button>
			</form>';

		return $html;
	}

	/**
	 * [getLoggedInHTML description]
	 * @param  ModelUser $userModel [description]
	 * @param  [type]    $message   [description]
	 * @return [type]               [description]
	 */
	public function getLoggedInHTML(\Model\User $userModel, $message = null) {
		$html = '
			<form class="form-signin">
				<h2 class="form-signin-heading">Laborationskod xx22aa</h2>
				<h3> ' . $userModel->getUsername() . '</h3>';

		if ($message) {
			$html .= "<p>$message</p>";
		}

		$html .= "<a href='?logout'>Logga ut</a>
				</form>";

		return $html;
	}


	private function getCleanInput($inputName) {
		if (isset($_POST[$inputName]) == false) {
			return "";
		}

		return $this->sanitize($_POST[$inputName]);
	}

	private function sanitize($input) {
		$temp = trim($input);
		return filter_var($temp, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	}
}