<?php

namespace form\view;

require_once("./src/form/view/FormView.php");
require_once("./src/form/model/QuestionViewCredentials.php");
require_once("./src/form/model/AnswerType.php");
require_once("./src/form/model/AnswerViewCredentials.php");


/**
 * @author Peter Emilsson
 * Common class for answering, viewing result, edit answers of a form
 */
abstract class SubmittedForm extends \form\view\FormView {

	/**
	 * @var \form\model\Form
	 */
	protected $form;

	/**
	 * @var string
	 */
	protected static $commentPOST = "comment";

	/**
	 * @var string
	 */
	protected static $answerTextPOST = "answerText";

	/**
	 * @return int id
	 * @throws \Exception If no id is found in url
	 */
	public function getSubmittedFormId() {
		$idGET = $this->navigationView->getShowForm();
		if (empty($_GET[$idGET]))
			throw new \Exception('No submitted form id in url');
		return $_GET[$idGET];
	}

	/**
	 * @param  \form\model\Form $form
	 * @return array of \form\model\QuestionViewCredentials
	 */
	public function getAnswers(\form\model\Form $form) {
		$questions = $form->getQuestions();
		$qViewCredArray = array();

		foreach ($questions as $key => $question) {
			$qId = $question->getId();
			$qViewCred = $this->getAnswer($question);
			if ($qViewCred !== null) 
				$qViewCredArray[] = $qViewCred;
		}
		return $qViewCredArray;
	}

	/**
	 * @param  \form\model\QuestionCredentials $question
	 * @return \form\model\QuestionViewCredentials       
	 * @throws \form\model\exception\StringLength If answer input is not valid
	 * @throws \Exception If question is required         
	 */
	protected function getAnswer(\form\model\QuestionCredentials $question) {
		$qId = $question->getId();
		try {
			$answerId = $this->getSelectedAnswer($question);
			$answer = $question->getAnswerById($answerId);
			$text = null;

			if (\form\model\AnswerType::GetName(1) == $answer->getType()) {
				$text = $this->getAnswerInput($answerId);
			}

			$answer = new \form\model\AnswerViewCredentials($answerId, $text);
			$commentText = $this->getQuestionComment($qId);

			return \form\model\QuestionViewCredentials::createBasic($qId, $answer, $commentText);
		} catch (\common\model\exception\StringLength $e) {
			throw $e;
		} catch (\Exception $e) {
			if ($question->isRequired()) {
				throw $e;
			}
		}
	}

	/**
	 * @param  int $answerId
	 * @return String         
	 */
	protected function getAnswerInput($answerId) {
		$aText = self::$answerTextPOST;
		if (isset($_POST["$aText$answerId"])) {
			return \common\Filter::sanitizeString($_POST["$aText$answerId"]);
		} else {
			return "";
		}
	}

	/**
	 * @param  \form\model\QuestionCredentials $question
	 * @return int   
	 * @throws \Exception If no answer id is found in post   
	 */
	protected function getSelectedAnswer($question) {
		$qId = $question->getId();
		if (isset($_POST["$qId"])) {
			return $_POST["$qId"];
		}
		throw new \Exception();
	}

	/**
	 * @param  int  $qId
	 * @param  string  $text
	 * @param  boolean $edit
	 * @return string HTML
	 */
	protected function getCommentTextHTML($qId, $edit = false, $text = null) {
		$maxLength = \form\model\QuestionViewCredentials::MaxCommentLength;
		$commentPost = self::$commentPOST;
		$disabled = "";
		if ($text !== null && !$edit) {
			$disabled = "disabled";
		} else if ($text === null && !$edit) {
			$text = $this->getQuestionComment($qId);
		}
		return "<textarea class='form-control' rows='3' maxlength='$maxLength' name='$commentPost$qId' placeholder='Optional comment' $disabled>$text</textarea>";
	}

	/**
	 * @param  int $qId
	 * @return String
	 */
	protected function getQuestionComment($qId) {
		$commentPost = self::$commentPOST;
		if (isset($_POST["$commentPost$qId"])) {
			return \common\Filter::sanitizeString($_POST["$commentPost$qId"]);
		} else {
			return "";
		}
	}

	/**
	 * @param  \form\model\QuestionCredentials $question
	 * @return string HTML                          
	 */
	protected function getAnswersHTML(\form\model\QuestionCredentials $question) {
		$answerCredentialsArray = $question->getAnswers();
		$qId = $question->getId();
		$html = "";
		$selectedAnswerId = null;

		//Check for validation errors
		if ($this->isSubmitning()) {
			try {
				$this->getAnswer($question);
				try {
					$selectedAnswerId = $this->getSelectedAnswer($question);
				} catch (\Exception $e) {
					
				}
			} catch (\common\model\exception\StringLength $e) {
     			$selectedAnswerId = $this->getSelectedAnswer($question);

				$maxLength = $e->getMaxLength();
				$minLength = $e->getMinLength();
				$html = "
						<div class='alert alert-danger'>
					 		Accepted length: $minLength-$maxLength characters
					 	</div>";	
			} catch (\Exception $e) {
				$html .= "
						<div class='alert alert-danger'>
							This question is required!	
						</div>";
			} 
		}
		
		foreach ($answerCredentialsArray as $answerCredentials) {
			$html .= $this->getAnswerHTML($answerCredentials, $selectedAnswerId, $qId);			
 		}

 		return $html;
	}		

	/**
	 * @param  form\model\AnswerCredentials $answerCredentials
	 * @param  int $selectedAnswerId
	 * @param  int $qId              
	 * @return string HTML
	 */
	protected function getAnswerHTML($answerCredentials, $selectedAnswerId, $qId) {
		$html = "";
		$answerTextPost = self::$answerTextPOST;	
		$id = $answerCredentials->getId();
		$title = $answerCredentials->getTitle();
		$checked = "";
		$maxLength = \form\model\AnswerViewCredentials::MaxStringLength;
		if ($selectedAnswerId !== null &&
 			$selectedAnswerId == $id) {
 			$checked = "checked";	
		} 

		$html .= "
				<div class='form-group'>
					<div class='radio'>
 						<label>
    						<input type='radio' name='$qId' value='$id' $checked>
    						$title
  						</label>
					</div>";  
		//If the answer requires text input from user
		if (\form\model\AnswerType::GetName(1) == $answerCredentials->getType()) {
			$answerText = $this->getAnswerInput($id);
			$html .= "<input type='text' class='form-control' value='$answerText' 
						name='$answerTextPost$id' placeholder='Enter your answer'
						maxlength='$maxLength'>";
		} 
		$html .= "</div>";
      				
 		return $html;
	}	

	/**
	 * Observer implementation
	 */
	
	public function getFailed($fId = null, $qId = null) {
		$this->saveMessage(1203);
		$this->navigationView->goToHome();
		exit();//Exit script
	}

	public function saveFailed($fId = null, $qId = null) {
		$this->saveMessage(1206);
	}

	public function saveOk($fId = null, $qId = null) {
		$this->saveMessage(1205);
		$this->navigationView->goToShowSubmittedForm($this->getFormId(), $fId);
		exit();//Exit script
	}

	public function notPublic() {
		$this->saveMessage(1207);
		$this->navigationView->goToHome();
		exit();//Exit script
	}

	public function notActive() {
		$this->saveMessage(1208);
		$this->navigationView->goToHome();
		exit();//Exit script
	}
}