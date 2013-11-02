<?php

namespace form\view;

require_once("./src/form/view/SubmittedForm.php");

/**
 * @author Peter Emilsson
 * Responsible for answering a form
 */
class AnswerForm extends \form\view\SubmittedForm {

	/**
	 * @param  \form\model\Form $form
	 * @return string HTML         
	 */
	public function getHTML(\form\model\Form $form) {  
		$this->form = $form;
		$html = $this->displayMessages();
		$html .= $this->getFormHead();
		$html .= $this->getQuestionsHTML();
		$html .= $this->getFormFooter();
		return $html;
	}

	/**
	 * @return stirng HTML
	 */
	private function getFormHead() {
		$title = $this->form->getTitle();
		$description = $this->form->getDescription();
		return "
			<form action='" . $this->navigationView->getGoToFormLink($this->getFormId()) . "' method='post' enctype='multipart/form-data'>
				<h2>$title</h2>
				<p>$description</p>
				<fieldset>
					<legend>Fill in the form</legend>";
	}

	/**
	 * @return string HTML
	 */
	private function getFormFooter() {
		$listLink = $this->navigationView->getListFormsLink();	
		return "
				</fieldset>
				<input type='submit' value='Submit Answers' class='btn btn-success'>
				<a href='$listLink' class='btn btn-warning'>Cancel</a>
			</form>";
	}

	/**
	 * @return string HTML
	 */
	private function getQuestionsHTML() {
		$html = "";
		$qCredArray = $this->form->getQuestions();
		foreach ($qCredArray as $key => $qCred) {
			$title = $qCred->getTitle();
			$description = $qCred->getDescription();
			$id = $qCred->getId();
			$required = $qCred->isRequired() ? "*" : "";
			$nr = $key + 1;
			$html .= "
					<h4>$nr: $title$required</h4>
					<p>$description</p>";

			$html .= $this->getAnswersHTML($qCred);
			if ($qCred->commentText()) {
				$html .= $this->getCommentTextHTML($id);
			}
			$html .= "<hr/>";	
		}
		return $html;
	}
}