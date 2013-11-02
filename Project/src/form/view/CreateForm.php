<?php

namespace form\view;

require_once("./src/form/view/Create.php");

/**
 * @author Peter Emilsson
 * Class responsible for creating and editing and displaying a form
 */
class CreateForm extends \form\view\Create {

	/**
	 * End date POST
	 * @var string
	 */
	private static $formEndDatePOST = "View::Form::EndDate";

	/**
	 * End date error message
	 * @var string
	 */
	private $dateErrorMessage = null;

	/**
	 * @return \form\model\Form
	 */
	public function getForm() {
		$title = $this->getTitle();
		$description = $this->getDescription();
		$endDate = $this->getEndDate();
		try {
			$formId = $this->getFormId();
			$form = \form\model\Form::createBasic($title, $description, $endDate);
			$form->setId($formId);
			return $form;
		} catch (\Exception $e) {
			return \form\model\Form::createBasic($title, $description, $endDate);	
		}
	}

	/**
	 * @param  \form\model\Form $form
	 * @return string HTML
	 */
	public function getHTML(\form\model\Form $form = null) {
		$html = $this->displayMessages();
		$stringMinLength = \form\model\Form::MinStringLength;
		$maxTitleLength = \form\model\Form::MaxTitleLength;
		$maxDescriptionLength = \form\model\Form::MaxDescriptionLength;
		$cancelLink;
		$actionLink;
		$legend;

		if ($form !== null) {
			$actionLink = $this->navigationView->getEditFormLink($form->getId());
			$cancelLink = $this->navigationView->getGoToManageFormLink($form->getId());
			$legend = "Edit Form";
		}
		else {
			$actionLink = $this->navigationView->getGoToCreateFormLink();
			$cancelLink = $this->navigationView->getGoToHomeLink();
			$legend = "Create new Form";
		}

		$html .= "<div class='row'>
				<div class='col-xs-12 col-sm-10 col-lg-8'>
				<form role='form' action='" . $actionLink . "' method='post' enctype='multipart/form-data'>
					<fieldset>
					<legend>$legend</legend>";

		if ($form !== null) {
			$html .= $this->getTitleTag($stringMinLength, $maxTitleLength, $form->getTitle());
			$html .= $this->getDescriptionTag($stringMinLength, $maxDescriptionLength, false, $form->getDescription());		
			$html .= $this->getEndDateTag($form->getEndDate());
		} else {
			$html .= $this->getTitleTag($stringMinLength, $maxTitleLength);
			$html .= $this->getDescriptionTag($stringMinLength, $maxDescriptionLength);		
			$html .= $this->getEndDateTag();
		}
		
		$html .= "
					<input type='submit' value='Save' class='btn btn-success'>
					<a href='$cancelLink' class='btn btn-warning'>Cancel</a>
				</fieldset>
				</form>
				</div>
				</div>";	
		return $html;
	}

	/**
	 * @param  \common\model\CustomDateTime $endDate 
	 * @return string HTML
	 */
	private function getEndDateTag(\common\model\CustomDateTime $endDate = null) {
		if ($endDate == null)
			$endDate = $this->getEndDate();

		$validation = "data-validation='custom' 
						data-validation-regexp='" . substr(\form\model\Form::DatePattern, 1, -1) . "'";
		$errorClass = "";
		$errorMessage = "";
		$autoFocus = "";
		if ($this->dateErrorMessage != null) {
			$errorClass = "has-error";
			$errorMessage = $this->dateErrorMessage;
			$autoFocus = "autofocus";
		}
		return "
			<div class='form-group $errorClass'>	
				<label for='endDateId' class='control-label'>Ends: $errorMessage</label>
				<input type='text' value='$endDate' name='" . self::$formEndDatePOST . "' id='endDateId' class='form-control' placeholder='Ends' $autoFocus $validation>
			</div>";
	}

	/**
	 * @param  \form\model\Form $form
	 * @return string HTML
	 * @todo split up in more than 1 function
	 */
	public function getFixedHTML(\form\model\Form $form) {
		$html = $this->displayMessages();
		$title = $form->getTitle();
		$description = $form->getDescription();
		$endDate = $form->getEndDate();
		$id = $form->getId();
		$publishLink = $this->navigationView->getPublishFormLink($id);
		$published = $form->isPublished() ? "<span class='label label-success'>Yes</span>" : "<span class='label label-warning'>No</span>";
		$publishButtonText = $form->isPublished() ? "Make private" : "Publish";
		$ended = $form->isActive() ? "<span class='label label-warning'>No</span>" : "<span class='label label-success'>Yes</span>";
		
		$html .= "
			<div class='panel panel-default'>
				<div class='panel-heading'>
					<h1 class='panel-title'>$title</h1>
				</div>
  				<div class='panel-body'>
  					<div class='row'>
  					<div class='col-xs-10 col-sm-10 col-lg-10'>
						<p>$description</p>
						<ul class='list-unstyled'>
							<li>Ended: $ended</li>
							<li>End date: $endDate</li>
							<li>Published: $published</li>
						</ul>	
					</div>
					<div class='col-xs-2 col-sm-2 col-lg-2'>
					<div class='row col-lg-12'>
						<form method='POST' action='$publishLink' class='pull-right'>	
							<input type='submit' class='btn btn-primary' value='$publishButtonText'>
						</form>
						</div>
						<div class='row col-lg-12'>
							<a href='" . $this->navigationView->getEditFormLink($id) . "' title='Edit Form' class='btn btn-primary pull-right'>
							<span class='glyphicon glyphicon-pencil'></span></a>
						</div>
						<div class='row col-lg-12'>
							<a href='" . $this->navigationView->getDeleteFormLink($id) . "' title='Delete Form' class='btn btn-danger pull-right'>
							<span class='glyphicon glyphicon-trash'></span></a>
						</div>
						
					</div>
					</div>
					<div class='row'>
					<hr/>
					<h2 class='col-xs-10 col-sm-10 col-lg-10'>Questions</h2>
					<div class='col-xs-2 col-sm-2 col-lg-2'>
						<div class='row col-lg-12'>
							<a href='" . $this->navigationView->getAddQuestionLink($id) . "' title='Add Question' class='btn btn-primary pull-right'>
							Add <span class='glyphicon glyphicon-plus-sign'></span></a>
						</div>
					</div>
					</div>";

		$questions = $form->getQuestions();
		if (count($questions) > 0) {
			$html .= $this->getFixedQuestionsHTML($questions, $id);	
		} else {
			$html .= "<p>No Questions</p>";
		}
		$html .= "</div></div>";
		
		return $html;
	}

	/**
	 * @param  array of \form\model\QuestionCredentials $qCredArray
	 * @param  int $formId       
	 * @return string HTML
	 */
	private function getFixedQuestionsHTML($qCredArray, $formId) {
		$html = "";
		foreach ($qCredArray as $key => $question) {
			$key++;
			$qTitle = $question->getTitle();
			$qDescription = $question->getDescription();
			$qId = $question->getId();
			$required = $question->isRequired() ? "<span class='label label-success'>Yes</span>" : "<span class='label label-warning'>No</span>";
			$commentText = $question->commentText() ? "<span class='label label-success'>Yes</span>" : "<span class='label label-warning'>No</span>";
			$html .= "
				<div class='row'>
					<div class='col-xs-10 col-sm-10 col-lg-10'>
						<div class='row col-lg-12'>
							<h3>$key: $qTitle</h3>
							<p>$qDescription</p>
							<p>Required: $required</p>
							<p>Comment Text: $commentText</p>
						</div>
						<div class='row col-lg-12'>
							<h4>Answers</h4>";
			$html .= $this->getFixedAnswersHTML($question->getAnswers());
			$html .= "	</div>
					</div>
					<div class='col-xs-2 col-sm-2 col-lg-2'>
						<div class='row col-lg-12'>
							<a href='" . $this->navigationView->getEditQuestionLink($formId, $qId) . "' title='Edit Question' class='btn btn-primary pull-right'>
							<span class='glyphicon glyphicon-pencil'></span></a>
						</div>
						<div class='row col-lg-12'>
							<a href='" . $this->navigationView->getDeleteQuestionLink($formId, $qId) . "' title='Delete Question' class='btn btn-danger pull-right'>
							<span class='glyphicon glyphicon-trash'></span></a>
						</div>
					</div>
				</div>
				<hr/>";
		}
		return $html;
	}

	/**
	 * @param  array of \form\model\AnswerCredentials $aCredArray [description]
	 * @return string HTML
	 */
	private function getFixedAnswersHTML($aCredArray) {
		$html = "<div class='col-xs-12 col-sm-8 col-lg-4'>
				<table class='table table-condensed'>
					<thead>
						<tr>
							<th>#</th>
							<th>Title</th>
							<th>Type</th>
						</tr>
					</thead>
					<tbody>";
		foreach ($aCredArray as $key => $answer) {
			$title = $answer->getTitle();
			$type = $answer->getType();
			$key++;
			$html .= "
				<tr>
					<td>$key</td>
					<td>$title</td>
					<td> $type</td>
				</tr>";
		}
		$html .= "</tbody>
				</table>
				</div>";
		return $html;
	}

	/**
	 * Set date error message
	 */
	public function dateError() {
		$this->dateErrorMessage = "Invalid Date Format";
	}

	/**
	 * @return string
	 */
	private function getEndDate() {
		if (isset($_POST[self::$formEndDatePOST]))
			return \common\Filter::sanitizeString($_POST[self::$formEndDatePOST]);
		else
			return "";
	}

	/**
	 * Formobserver implemetation
	 */

	public function saveOk($fId = null, $qId = null) {
		$this->saveMessage(1201);
		$this->navigationView->goToManageForm($fId);
		exit();//Exit script
	}

	public function saveFailed($fId = null, $qId = null) {
		$this->saveMessage(1202);
		try {
			$fId = $this->getFormdId();
			$this->navigationView->goToManageForm($fId);
		} catch (\Exception $e) {
			$this->navigationView->goToHome();
		}
		exit();//Exit script
	}

	public function getFailed($fId = null, $qId = null) {
		$this->saveMessage(1203);
		$this->navigationView->goToHome();
		exit();//Exit script
	}

	public function publishOk($fId) {
		$this->saveMessage(1210);
		$this->navigationView->goToManageForm($fId);
		exit();//Exit script
	}

	public function publishFailed($fId) {
		$this->saveMessage(1209);
		$this->navigationView->goToManageForm($fId);
		exit();//Exit script
	}

	public function noQuestions($fId) {
		$this->saveMessage(1212);
		$this->navigationView->goToManageForm($fId);
		exit();//Exit script
	}
}