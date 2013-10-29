<?php

namespace form\view;

require_once("./src/form/view/SubmittedForm.php");

class ManageSubmittedForm extends \form\view\SubmittedForm {

	private $submittedFormCredentials;

	public function edit() {
		return isset($_GET[$this->navigationView->getForm()]) &&
			isset($_GET[$this->navigationView->getShowForm()]) &&
			isset($_GET['edit']);
	}

	public function getHTML($form, $submittedFormCredentials, $edit) {
		$this->submittedFormCredentials = $submittedFormCredentials;
		$this->form = $form;
		$html = $this->getFormHead($edit);
		$html .= $this->getQuestionsHTML($edit);
		if ($edit)
			$html .= $this->getFormFooter($edit);
		return $html;
	}

	private function getFormHead($edit) {
		$formId = $this->submittedFormCredentials->getFormId();
		$userFormId = $this->submittedFormCredentials->getUserFormId();
		$formTitle = $this->submittedFormCredentials->getTitle();
		$formDescription = $this->submittedFormCredentials->getDescription();
		$formSubmitted = $this->submittedFormCredentials->getSubmittedDate();
		$lastUpdated = $this->submittedFormCredentials->getLastUpdatedDate();
		$editLink = $this->navigationView->getEditSubmittedFormLink($formId, $userFormId);
		$endDate = $this->form->getFormCredentials()->getEndDate();//Train wreck

		$html = "
				<h2>$formTitle</h2>
				<p class='lead'>$formDescription</p>
				<p>Submitted: $formSubmitted</p>
				<p>Updated: $lastUpdated</p>";

		if ($edit) {
			$html .= "
				<p>Ends: $endDate</p>
				<h3>Questions</h3>
				<form action='$editLink' method='post' enctype='multipart/form-data'>
				<fieldset>
					<legend>Update your answers</legend>";
		} else {
			if (strtotime($endDate) > time()) {
				$html .= "
					<p>Ends: $endDate</p>
					<p><a href='$editLink' class='btn btn-primary'>Edit</a></p>";
			} else {
				$html .= "<p>Ended: $endDate</p>";
			}
			$html .= "
				<h3>Questions</h3>";
		}

		return $html;
	}

	private function getFormFooter($edit) {
		$html;
		$formId = $this->submittedFormCredentials->getFormId();
		$userFormId = $this->submittedFormCredentials->getUserFormId();
		$link = $this->navigationView->getShowSubmittedFormLink($formId, $userFormId);
		
		$html = "
				</fieldset>
				<input type='submit' value='Save' class='btn btn-primary'>
				<a href='$link' class='btn btn-warning'>Cancel</a>
			</form>";

		return $html;
	}

	private function getQuestionsHTML($edit) {
		$html = "";
		foreach ($this->form->getQuestions() as $key => $question) {
			$title = $question->getTitle();
			$description = $question->getDescription();
			$id = $question->getId();
			$answers = $question->getAnswers();
			$nr = $key + 1;
			$html .= "
					<h4>$nr: $title</h4>
					<p>$description</p>";

			if ($this->isSubmitning()) {
				$html .= $this->getErrorAnswersHTML($answers, $id);
			} else {
				$html .= $this->getAnswersHTML($answers, $id, $this->submittedFormCredentials, $edit);
			}
			
			$html .= "<hr/>";	
		}
		return $html;
	}		

	private function getAnswersHTML($answers, $qId, $formAnswers, $edit) {
		$html = "";
		$disabled = $edit ? "" : "disabled";
		foreach ($answers as $key => $answer) {
			$id = $answer->getId();
			$title = $answer->getTitle();
			$check = "";
			foreach ($formAnswers->getAnswersResult() as $key2 => $value) {
				if ($value->compare($qId, $id)) {
					$check = "checked";
					break;
				}
			}

			$html .= "
				<div class='input-group'>
     				<span class='input-group-addon'>
     					<input type='radio' name='$qId' value='$id' $check $disabled>
					</span>
      				<span class='form-control'>$title</span>
    			</div><!-- /input-group -->";
 		}
 		return $html;
	}

	private function getErrorAnswersHTML($answers, $qId) {
		$html = "";
		$aId = null;
		try {
			$answerCred = $this->getAnswer($qId);
			$aId = $answerCred->getAnswerId();
		} catch (\Exception $e) {
			$html .= "
					<div class='alert alert-danger'>
						This question is required!	
					</div>";
		}
		
		foreach ($answers as $key => $answer) {
			$id = $answer->getId();
			$title = $answer->getTitle();
			$html .= "
				<div class='input-group'>
     				<span class='input-group-addon'>";

     		if ($aId !== null && $aId == $id) {
				$html .= "<input type='radio' name='$qId' value='$id' checked='true'>";
			} else {
				$html .= "<input type='radio' name='$qId' value='$id'>";
			}

			$html .= "
					</span>
      				<span class='form-control'>$title</span>
    			</div><!-- /input-group -->";
 		}
 		return $html;
	}				

	public function addFormOk(\form\model\FormCredentials $formCred = null) {
		$this->navigationView->goToHome();
	}
}