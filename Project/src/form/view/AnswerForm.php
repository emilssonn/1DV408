<?php

namespace form\view;

require_once("./src/form/view/SubmittedForm.php");
require_once("./src/form/model/AnswerViewCredentials.php");

class AnswerForm extends \form\view\SubmittedForm {

	public function getHTML($form) {  
		$this->form = $form;
		$html = $this->getFormHead();
		$html .= $this->getQuestionsHTML();
		$html .= $this->getFormFooter();
		return $html;
	}

	private function getFormHead() {
		$html;
		$formCred = $this->form->getFormCredentials();
		$title = $formCred->getTitle();
		$description = $formCred->getDescription();

		$html = "
			<form action='" . $this->navigationView->getGoToFormLink($this->getFormId()) . "' method='post' enctype='multipart/form-data'>
				<h2>$title</h2>
				<p>$description</p>
				<fieldset>
					<legend>Fill in the form</legend>";
		return $html;
	}

	private function getFormFooter() {
		$html;
		$listLink = $this->navigationView->getListFormsLink();
		
		$html = "
				</fieldset>
				<input type='submit' value='Submit' class='btn btn-primary'>
				<a href='$listLink' class='btn btn-warning'>Cancel</a>
			</form>";

		return $html;
	}

	private function getQuestionsHTML() {
		$html = "";
		$questions = $this->form->getQuestions();

		foreach ($questions as $key => $question) {
			$title = $question->getTitle();
			$description = $question->getDescription();
			$id = $question->getId();
			$answers = $question->getAnswers();
			$nr = $key + 1;
			$html .= "
					<h4>$nr: $title</h4>
					<p>$description</p>";

			$html .= $this->getAnswersHTML($answers, $id);
			$html .= "<hr/>";	
		}
		return $html;
	}

	private function getAnswersHTML($answers, $qId) {
		$html = "";
		$aId = null;
		if ($this->isSubmitning()) {
			try {
				$answerCred = $this->getAnswer($qId);
				$aId = $answerCred->getAnswerId();
			} catch (\Exception $e) {
				$html .= "
						<div class='alert alert-danger'>
							This question is required!	
						</div>";
			}
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

}