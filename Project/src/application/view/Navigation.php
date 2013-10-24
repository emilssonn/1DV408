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

	private static $homeGET = "home";

	private static $formGET = "form";

	private static $createFormGET = "create";

	private static $addQuestionGET = "question";

	private static $showFormGET = "show";

	private static $editFormGET = "edit";


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

	public function getForm() {
		return self::$formGET;
	}

	public function getShowForm() {
		return self::$showFormGET;
	}

	public function getCreateForm() {
		return self::$createFormGET;
	}

	public function getQuestion() {
		return self::$addQuestionGET;
	}

	public function loginWithReturnUrl() {
		header("Location: ?login&ref=" . $_SERVER["QUERY_STRING"]);
	}

	/**
	 * @return String HTML
	 */
	public function getLogoutButton() {
		return "<a href='?" . $this->getLogOut() . "'>Sign out</a>";
	}

	/**
	 * @return string HTML
	 */
	public function getRegisterLink() {
		$register = $this->getRegister();
		return "<a href='?$register'>Sign up</a>";
	}

	/**
	 * @return boolean
	 */
	public function wantsToRegister() {
		return isset($_GET[$this->getRegister()]);
	}

	public function userHome() {
		return isset($_GET[self::$homeGET]);
	}

	public function createForm() {
		return isset($_GET[self::$formGET]) && 
				isset($_GET[self::$createFormGET]);
	}

	public function editForm() {
		return isset($_GET[self::$formGET]) && 
				isset($_GET[self::$editFormGET]);
	}

	public function addQuestion() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$createFormGET]) && isset($_GET[self::$addQuestionGET]);
	}

	public function showForm() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$showFormGET]);
	}

	public function goToHome() {
		$home = self::$homeGET;
		header("Location: ?$home");
	}

	public function goToForm($id) {
		$form = self::$formGET;
		$show = self::$showFormGET;
		header("Location: ?$form=$id&$show");
	}

	public function goToEditForm($id) {
		$form = self::$formGET;
		$edit = self::$editFormGET;
		header("Location: ?$form=$id&$edit");
	}

}