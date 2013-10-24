<?php

namespace form\view;

require_once("./src/common/Filter.php");
require_once("./src/form/model/FormObserver.php");

class CreateForm implements \form\model\FormObserver {
	
	private $navigationView;

	private $message = array();

	private static $formTitlePOST = "View::Form::Title";

	private static $formDescriptionPOST = "View::Form::Description";

	private static $formEndDatePOST = "View::Form::EndDate";

	private $createFormHTML = true;

	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
	}

	public function getHTML(\form\model\Form $form = null) {
		$html;
		if ($this->createFormHTML) {

			$html = "
				<form action='?" . $this->navigationView->getForm() . "&" . $this->navigationView->getCreateForm() . "' method='post' enctype='multipart/form-data' class='form-signin'>
					<fieldset>
						<legend class='form-signin-heading'>Create new form</legend>
						
						<label for='titleID'>Title:</label>
						<input type='text' name='" . self::$formTitlePOST . "' id='titleID' class='form-control' placeholder='Title' autofocus>
						
						<label for='descriptionId'>Description:</label>
						<textarea name='" . self::$formDescriptionPOST . "' id='descriptionId' class='form-control' placeholder='Description'></textarea>
							
						<label for='endDateId'>End date:</label>
						<input type='text' name='" . self::$formEndDatePOST . "' id='endDateId' class='form-control' placeholder='End date'>

						<input type='submit' value='Create form' class='btn btn-lg btn-primary btn-block'>
					</fieldset>
				</form>";
		} else {
			$html = $this->getFixedHTML($form);
		}

		return $html;
	}

	private function getFixedHTML(\form\model\Form $form) {
		$formCred = $form->getFormCredentials();
		$title = $formCred->getTitle();
		$description = $formCred->getDescription();
		$endDate = $formCred->getEndDate();
		$id = $formCred->getId();
		$html = "
				<h2>$title</h2>
				<p>$description</p>
				<p>$endDate</p>
				<a href='?" . $this->navigationView->getForm() . "=$id&" . $this->navigationView->getCreateForm() . "&". $this->navigationView->getQuestion() . "' class='btn btn-lg btn-primary btn-block'>Add Question</a>
				";

		return $html;
	}

	/**
	 * @return boolean
	 */
	public function isCreating() {
		return $this->navigationView->createForm() &&
				strtolower($_SERVER['REQUEST_METHOD']) == "post";
	}

	public function isEditing() {
		return $this->navigationView->editForm();
	}

	public function getFormCredentials() {

		$title = $this->getTitle();
		$description = $this->getDescription();
		$endDate = $this->getEndDate();
		return \form\model\FormCredentials::createFormBasic($title, $description, $endDate);
	}

	public function getFormId() {
		$idGET = $this->navigationView->getForm();
		return $_GET[$idGET];
	}

	public function addFormOk(\form\model\FormCredentials $formCred) {
		$this->navigationView->goToEditForm($formCred->getId());
	}

	public function getFormFailed() {
		
	}

	public function addFormFailed() {

	}

	public function getFormOk() {
		$this->createFormHTML = false;
	}

	private function getTitle() {
		if (isset($_POST[self::$formTitlePOST]))
			return \Common\Filter::sanitizeString($_POST[self::$formTitlePOST]);
		else
			return "";
	}

	private function getDescription() {
		if (isset($_POST[self::$formDescriptionPOST]))
			return \Common\Filter::sanitizeString($_POST[self::$formDescriptionPOST]);
		else
			return "";
	}

	private function getEndDate() {
		if (isset($_POST[self::$formEndDatePOST]))
			return \Common\Filter::sanitizeString($_POST[self::$formEndDatePOST]);
		else
			return "";
	}
}