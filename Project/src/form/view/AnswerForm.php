<?php

namespace form\view;

require_once("./src/form/model/FormObserver.php");
require_once("./src/form/model/AnswerViewCredentials.php");

class AnswerForm implements \form\model\FormObserver {

	private $navigationView;

	private $form;

	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
	}

	public function isSubmitning() {
		return $this->navigationView->answerForm() &&
			strtolower($_SERVER['REQUEST_METHOD']) == "post";
	}

	public function getAnswers(\form\model\Form $form) {
		$questions = $form->getQuestions();
		$answers = array();

		foreach ($questions as $key => $question) {
			$qId = $question->getId();
			$answers[] = $this->getAnswer($qId);
		}
		return $answers;
	}

	private function getAnswer($qId) {
		if (isset($_POST["$qId"])) {
			$answerId = $_POST["$qId"];
			return new \form\model\AnswerViewCredentials($qId, $answerId);
		} else {
			throw new \Exception();
		}
	}

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
		$homeLink = $this->navigationView->getGoToHomeLink();
		
		$html = "
					<input type='submit' value='Submit' class='btn btn-lg btn-primary btn-block'>
				</fieldset>
			</form>
			<a href='$homeLink'>Cancel</a>";

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

			$html .= "
					<h4>$title</h4>
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

	public function getFormId() {
		$idGET = $this->navigationView->getForm();
		if (empty($_GET[$idGET]))
			throw new \Exception('No form id in url');
		return $_GET[$idGET];
	}

	public function addFormOk(\form\model\FormCredentials $formCred) {

	}

	public function addFormFailed() {

	}

	public function getFormOk() {

	}

	public function getFormFailed() {

	}

}