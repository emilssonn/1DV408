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
			$homeLink = $this->navigationView->getGoToHomeLink();
			$html = "
				<form role='form' action='" . $this->navigationView->getGoToCreateFormLink() . "' method='post' enctype='multipart/form-data'>
					<fieldset>
						<legend>Create new form</legend>
						
						<div class='form-group'>
							<label for='titleID'>Title:</label>
							<input type='text' name='" . self::$formTitlePOST . "' id='titleID' class='form-control' placeholder='Title' autofocus>
						</div>

						<div class='form-group'>
							<label for='descriptionId'>Description:</label>
							<textarea name='" . self::$formDescriptionPOST . "' id='descriptionId' class='form-control' placeholder='Description'></textarea>
						</div>

						<div class='form-group'>	
							<label for='endDateId'>End date:</label>
							<input type='text' name='" . self::$formEndDatePOST . "' id='endDateId' class='form-control' placeholder='End date'>
						</div>

						<input type='submit' value='Create form' class='btn btn-success'>
						<a href='$homeLink' class='btn btn-warning'>Cancel</a>
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
		$published = $formCred->isPublished();

		$html = "
				<h3>Title</h3>
					$title
				<h4>Description</h4>
					$description
				<h4>End date</h4>
					$endDate
				";

		if ($published) {
			$html .= "<p>Published: Yes</p>";
		} else {
			$html .= "<p>Published: No</p>";
		}
		
		$html .= "
			<a href='" . $this->navigationView->getAddQuestionLink($id) . "' class='btn btn-primary'>Add Question</a>
			<h4>Questions</h4>";

		$questions = $form->getQuestions();
		if (count($questions) > 0) {
			foreach ($form->getQuestions() as $question) {
				$qTitle = $question->getTitle();
				$qDescription = $question->getDescription();
				$qId = $question->getId();
				$html .= "
					<h5>$qTitle</h5>
					<p>$qDescription</p>
					<a href='" . $this->navigationView->getEditQuestionLink($id, $qId) . "' class='btn btn-primary'>Edit</a>";
			}
		} else {
			$html .= "<p>No Questions</p>";
		}
		

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