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

	private static $listFormsGET = "list";

	private static $myFormsGET = "my";

	private static $formResultGET ="result";


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

	public function getGoToFormLink($id) {
		$form = self::$formGET;
		$show = self::$showFormGET;
		return "?$form=$id&$show";
	}

	public function getGoToEditFormLink($id) {
		$form = self::$formGET;
		$edit = self::$editFormGET;
		return "?$form=$id&$edit";
	}

	public function getGoToCreateFormLink() {
		$form = self::$formGET;
		$create = self::$createFormGET;
		return "?$form&$create";
	}

	public function getGoToHomeLink() {
		return "?home";
	}

	public function getListFormsLink() {
		$list = self::$listFormsGET;
		$form = self::$formGET;
		return "?$form&$list";
	}

	public function getListMyFormsLink() {
		$list = self::$listFormsGET;
		$form = self::$formGET;
		$my = self::$myFormsGET;
		return "?$form&$list&$my";
	}

	public function getFormResultLink($id) {
		$form = self::$formGET;
		$result = self::$formResultGET;
		return "?$form=$id&$result";
	}

	public function getAddQuestionLink($fid) {
		$form = self::$formGET;
		$create = self::$createFormGET;
		$add = self::$addQuestionGET;
		return "?$form=$fid&$create&$add";
	}

	public function getEditQuestionLink($fId, $qId) {
		$form = self::$formGET;
		$edit = self::$editFormGET;
		$add = self::$addQuestionGET;
		return "?$form=$fId&$edit&$add=$qId";
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

	public function editQuestion() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$editFormGET]) && isset($_GET[self::$addQuestionGET]);
	}

	public function listForms() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$listFormsGET]);
	}

	public function listMyForms() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$listFormsGET]) && isset($_GET[self::$myFormsGET]);
	}

	public function answerForm() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$showFormGET]);
	}

	public function viewResults() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$formResultGET]);
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