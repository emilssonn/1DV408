<?php

namespace form\view;

require_once("./src/form/view/SubmittedForm.php");

/**
 * @author Peter Emilsson
 * Responsible for viewing personal results for a form and editing answers
 */
class ManageSubmittedForm extends \form\view\SubmittedForm {

	/**
	 * @var \form\model\SubmittedFormCredentials
	 */
	private $submittedFormCredentials;

	/**
	 * @return bool
	 */
	public function edit() {
		return isset($_GET[$this->navigationView->getForm()]) &&
			isset($_GET[$this->navigationView->getShowForm()]) &&
			isset($_GET[$this->navigationView->getEdit()]);
	}

	/**
	 * @param  \form\model\Form                     $form                     
	 * @param  \form\model\SubmittedFormCredentials $submittedFormCredentials 
	 * @param  bool                         	  	$edit                    
	 * @return string HTML                                                  
	 */
	public function getHTML(\form\model\Form $form, 
							\form\model\SubmittedFormCredentials $submittedFormCredentials, 
							$edit) {
		$this->submittedFormCredentials = $submittedFormCredentials;
		$this->form = $form;
		$html = $this->displayMessages();
		$html .= $this->getFormHead($edit);
		$html .= $this->getQuestionsHTML($edit);
		if ($edit)
			$html .= $this->getFormFooter();
		return $html;
	}

	/**
	 * @param  bool $edit 
	 * @return string HTML
	 */
	private function getFormHead($edit) {
		$formId = $this->submittedFormCredentials->getFormId();
		$userFormId = $this->submittedFormCredentials->getUserFormId();
		$formTitle = $this->submittedFormCredentials->getTitle();
		$formDescription = $this->submittedFormCredentials->getDescription();
		$formSubmitted = $this->submittedFormCredentials->getSubmittedDate();
		$lastUpdated = $this->submittedFormCredentials->getLastUpdatedDate();
		$editLink = $this->navigationView->getEditSubmittedFormLink($formId, $userFormId); 
		$endDate = $this->submittedFormCredentials->getEndDate();
		$html = "
				<h2>$formTitle</h2>
				<p class='lead'>$formDescription</p>
				<p>Submitted: $formSubmitted</p>
				<p>Updated: $lastUpdated</p>";

		if ($edit) {
			$html .= "
				<h3>Questions</h3>
				<form action='$editLink' method='post' enctype='multipart/form-data'>
				<fieldset>
					<legend>Update your answers</legend>";
		} else {
			if (!$endDate->hasPassed() && $this->form->isPublished()) {
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

	/**
	 * @return string HTML
	 */
	private function getFormFooter() {
		$formId = $this->submittedFormCredentials->getFormId();
		$userFormId = $this->submittedFormCredentials->getUserFormId();
		$link = $this->navigationView->getShowSubmittedFormLink($formId, $userFormId);
		return "
				</fieldset>
				<input type='submit' value='Save' class='btn btn-primary'>
				<a href='$link' class='btn btn-warning'>Cancel</a>
			</form>";
	}

	/**
	 * @param  bool $edit
	 * @return string HTML, all questions for one form   
	 */
	private function getQuestionsHTML($edit) {
		$html = "";
		$qCredArray = $this->form->getQuestions();
		foreach ($qCredArray as $key => $qCred) {
			$title = $qCred->getTitle();
			$description = $qCred->getDescription();
			$id = $qCred->getId();
			$required = $qCred->isRequired() ? "*" : "";
			$aCredArray = $qCred->getAnswers();
			$nr = $key + 1;
			$html .= "
					<h4>$nr: $title$required</h4>
					<p>$description</p>";

			if ($this->isSubmitning()) {
				$html .= $this->getAnswersHTML($qCred);
			} else {
				$html .= $this->getManageAnswersHTML($aCredArray, $id, $edit);
			}
			if ($qCred->commentText()) {
				//Find if the question has been answered, returns it if found
				$object = array_filter(
    				$this->submittedFormCredentials->getAnswersResult(),
    				function ($e) use (&$id) {
        				return $e->getQuestionId() == $id;
    				}
				);
				if (count($object) > 0) {
					//[reset] get first element in array
					$html .= $this->getCommentTextHTML($id, $edit, reset($object)->getCommentText());
				} else {
					$html .= $this->getCommentTextHTML($id, $edit);
				}
			}
			
			$html .= "<hr/>";	
		}
		return $html;
	}		

	/**
	 * @param  array of \form\model\AnswerCredentials $aCredArray
	 * @param  int $qId     
	 * @param  bool $edit   
	 * @return string HTML, all answers for one question, the one previously answered checked         
	 */
	private function getManageAnswersHTML($aCredArray, $qId, $edit) {
		$html = "";
		$disabled = $edit ? "" : "disabled";
		$answerTextPost = self::$answerTextPOST;
		foreach ($aCredArray as $aCred) {
			$id = $aCred->getId();
			$title = $aCred->getTitle();
			$check = "";
			$previousACred = null; 
			//Check what answer to check
			foreach ($this->submittedFormCredentials->getAnswersResult() as $value) {
				if ($value->compare($qId, $id)) {
					$check = "checked";
					$previousACred = $value;
					break;
				}
			}

			$html .= "
				<div class='form-group'>
					<div class='radio'>
 						<label>
    						<input type='radio' name='$qId' value='$id' $check $disabled>
    						$title
  						</label>
					</div>";     	
			if (\form\model\AnswerType::GetName(1) == $aCred->getType()) {
				$text = "";
				if ($previousACred !== null) 
					$text = $previousACred->getAnswer()->getNoteText();
				if (!empty($text) || $edit)
					$html .= "<input type='text' class='form-control' name='$answerTextPost$id' value='$text' placeholder='Enter your answer' $disabled>";
				
			}		
			$html .= "</div>";	
 		}
 		return $html;
	}
}