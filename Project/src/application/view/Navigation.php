<?php

namespace application\view;

/**
 * @author Peter Emilsson
 */
class Navigation {
	
	/**
	 * Names in URL
	 * @var string
	 */
	private static $loginGET = "login";

	private static $logoutGET = "logout";

	private static $registerGET = "register";

	private static $homeGET = "home";

	private static $formGET = "form";

	private static $createGET = "create";

	private static $questionGET = "question";

	private static $showGET = "show";

	private static $editGET = "edit";

	private static $listFormsGET = "list";

	private static $myFormsGET = "my";

	private static $formResultGET = "result";

	private static $mySubmittedFormsGET = "submitted"; 

	private static $manageFormGET = "manage";

	private static $deleteGET = "delete";

	private static $publishFormGET = "publish";

	/**
	 * @return string
	 */
	public function getLogin() {
		return self::$loginGET;
	}

	public function getForm() {
		return self::$formGET;
	}

	public function getShowForm() {
		return self::$showGET;
	}

	public function getQuestion() {
		return self::$questionGET;
	}

	public function getLogOut() {
		return self::$logoutGET;
	}

	public function getRegister() {
		return self::$registerGET;
	}

	public function getEdit() {
		return self::$editGET;
	}

	/**
	 * -----------------------------------------------------
	 * Methods for getting links, ?xxxx
	 */	

	/**
	 * @return String
	 */
	public function getLogoutLink() {
		return "?" . self::$logoutGET;
	}

	/**
	 * @return string html
	 */
	public function getRegisterLink() {
		$register = $this->getRegister();
		return "<a href='?$register'>Sign up</a>";
	}

	/**
	 * @param  int $id 
	 * @return string
	 */
	public function getGoToFormLink($id) {
		$form = self::$formGET;
		$show = self::$showGET;
		return "?$form=$id&$show";
	}

	/**
	 * @param  int $formId    
	 * @param  int $userFormId 
	 * @return string             
	 */
	public function getShowSubmittedFormLink($formId, $userFormId) {
		$form = self::$formGET;
		$show = self::$showGET;
		return "?$form=$formId&$show=$userFormId";
	}

	/**
	 * @param  int $formId    
	 * @param  int $userFormId
	 * @return string           
	 */
	public function getEditSubmittedFormLink($formId, $userFormId) {
		$form = self::$formGET;
		$show = self::$showGET;
		$edit = self::$editGET;
		return "?$form=$formId&$show=$userFormId&$edit";
	}

	/**
	 * @param  int $id 
	 * @return string
	 */
	public function getGoToManageFormLink($id) {
		$form = self::$formGET;
		$manage = self::$manageFormGET;
		return "?$form=$id&$manage";
	}

	/**
	 * @return string
	 */
	public function getGoToCreateFormLink() {
		$form = self::$formGET;
		$create = self::$createGET;
		return "?$form&$create";
	}

	/**
	 * @return string
	 */
	public function getGoToHomeLink() {
		return "?home";
	}

	/**
	 * @return string
	 */
	public function getListFormsLink() {
		$list = self::$listFormsGET;
		$form = self::$formGET;
		return "?$form&$list";
	}

	/**
	 * @return string
	 */
	public function getListMyFormsLink() {
		$list = self::$listFormsGET;
		$form = self::$formGET;
		$my = self::$myFormsGET;
		return "?$form&$list&$my";
	}

	/**
	 * @param  int $id
	 * @return string
	 */
	public function getFormResultLink($id) {
		$form = self::$formGET;
		$result = self::$formResultGET;
		return "?$form=$id&$result";
	}

	/**
	 * @param  int $fid 
	 * @return string
	 */
	public function getAddQuestionLink($fid) {
		$form = self::$formGET;
		$create = self::$createGET;
		$question = self::$questionGET;
		return "?$form=$fid&$create&$question";
	}

	/**
	 * @param  int $fId
	 * @param  int $qId 
	 * @return string
	 */
	public function getEditQuestionLink($fId, $qId) {
		$form = self::$formGET;
		$edit = self::$editGET;
		$question = self::$questionGET;
		return "?$form=$fId&$edit&$question=$qId";
	}

	/**
	 * @param  int $fId 
	 * @param  int $qId 
	 * @return string      
	 */
	public function getDeleteQuestionLink($fId, $qId) {
		$delete = self::$deleteGET;
		$question = self::$questionGET;
		$form = self::$formGET;
		return "?$form=$fId&$delete&$question=$qId";
	}

	/**
	 * @param  int $fId 
	 * @return string      
	 */
	public function getDeleteFormLink($fId) {
		$delete = self::$deleteGET;
		$form = self::$formGET;
		return "?$form=$fId&$delete";
	}

	/**
	 * @return string
	 */
	public function getListMySubmittedFormsLink() {
		$list = self::$listFormsGET;
		$form = self::$formGET;
		$submitted = self::$mySubmittedFormsGET;
		return "?$form&$list&$submitted";
	}

	/**
	 * @param  int $fId 
	 * @return string      
	 */
	public function getPublishFormLink($fId) {
		$manage = self::$manageFormGET;
		$publish = self::$publishFormGET;
		$form = self::$formGET;
		return "?$form=$fId&$manage&$publish";
	}

	/**
	 * @param  int $fId string
	 * @return string      
	 */
	public function getEditFormLink($fId) {
		$form = self::$formGET;
		$edit = self::$editGET;
		return "?$form=$fId&$edit";
	}

	/**
	 * --------------------------------------------------------
	 * Check what page to display/controller to user
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
				isset($_GET[self::$createGET]);
	}

	public function manageForm() {
		return isset($_GET[self::$formGET]) && 
				isset($_GET[self::$manageFormGET]) && !isset($_GET[self::$showGET]);
	}

	public function addQuestion() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$createGET]) && isset($_GET[self::$questionGET]);
	}

	public function editQuestion() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$editGET]) && isset($_GET[self::$questionGET]);
	}

	public function editForm() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$editGET]) && !isset($_GET[self::$questionGET]) && !isset($_GET[self::$showGET]);
	}

	public function listForms() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$listFormsGET]);
	}

	public function listMyForms() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$listFormsGET]) && isset($_GET[self::$myFormsGET]);
	}

	public function answerForm() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$showGET]) && !isset($_GET[self::$editGET]) && empty($_GET[self::$showGET]);
	}

	public function viewResults() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$formResultGET]);
	}

	public function listMySubmittedForms() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$mySubmittedFormsGET]) && isset($_GET[self::$listFormsGET]);
	}

	public function showMySubmittedForm() {
		return isset($_GET[self::$formGET]) && isset($_GET[self::$showGET]);
	}

	public function delete() {
		return isset($_GET[self::$deleteGET]);
	}

	public function publishForm() {
		return $this->manageForm() &&
				isset($_GET[self::$publishFormGET]);
	}

	/**
	 * --------------------------------------------
	 * Header location methods
	 */

	public function goToHome() {
		$home = self::$homeGET;
		header("Location: ?$home");
	}

	/**
	 * @param  int $id
	 */
	public function goToForm($id) {
		$form = self::$formGET;
		$show = self::$showGET;
		header("Location: ?$form=$id&$show");
	}

	/**
	 * @param  int $id 
	 */
	public function goToManageForm($id) {
		$form = self::$formGET;
		$manage = self::$manageFormGET;
		header("Location: ?$form=$id&$manage");
	}

	/**
	 * @param  int $formId     
	 * @param  int $userFormId 
	 */
	public function goToShowSubmittedForm($formId, $userFormId) {
		$form = self::$formGET;
		$show = self::$showGET;
		header("Location: ?$form=$formId&$show=$userFormId");
	}

	public function loginWithReturnUrl() {
		header("Location: ?login&ref=" . $_SERVER["QUERY_STRING"]);
	}
}