<?php

namespace form\view;

require_once("./src/common/Filter.php");
require_once("./src/form/view/Create.php");
require_once("./src/form/model/QuestionCredentials.php");
require_once("./src/form/model/AnswerCredentials.php");

/**
 * @author Peter Emilsson
 * Class for creating and editing question related to a form.
 */
class CreateQuestion extends \form\view\Create {

	/**
	 * Answer type position in POST, used with a number/id
	 * @var string
	 */
	private static $answerTypePOST = "at";

	/**
	 * Answer title position in POST, used with a number/id
	 * @var string
	 */
	private static $answerTitlePOST = "as";

	/**
	 * If the question is required
	 * @var string
	 */
	private static $requiredPOST = "required";

	/**
	 * If the question should have a optional comment input
	 * @var string
	 */
	private static $commentTextPOST = "commentText"; 

	/**
	 * @return \form\model\QuestionCredentials
	 */
	public function getQuestionCredentials() {
		$title = $this->getTitle();
		$description = $this->getDescription();
		$required = $this->isRequired();
		$commentText = $this->commentText();
		$qCred;
		$aCredArray;
		try {
			$qId = $this->getQuestionId();
			$qCred =  \form\model\QuestionCredentials::createFull($title, $description, $qId, $required, $commentText);
			$aCredArray = $this->getAnswers();
		} catch (\Exception $e) {
			$qCred =  \form\model\QuestionCredentials::createBasic($title, $description, $required, $commentText);
			$aCredArray = $this->getAnswers();
		}
		
		$qCred->addAnswers($aCredArray);
		return $qCred;
	}

	/**
	 * @return array of \form\model\AnswerCredentials
	 */
	private function getAnswers() {
		$aCredArray = array();
		$values = $this->getAnswersValue();

		$i = 1;
		foreach ($values as $key => $value) {
			$type;
			$title;
			if (count($value) > 1) {
				$type = \form\model\AnswerType::GetName(1);
				$title = \common\Filter::sanitizeString($value[0]);
			} else {
				$type = \form\model\AnswerType::GetName(2);
				$title = \common\Filter::sanitizeString($value[0]);
			}
			$aCred = \form\model\AnswerCredentials::createFull($title, $type, $i, $key);
			$aCredArray[] = $aCred;
			$i++;
		}
		return $aCredArray;
	}

	/**
	 * @return int id
	 * @throws \Exception If if no id is found in url
	 */
	public function getQuestionId() {
		$idGET = $this->navigationView->getQuestion();
		if (empty($_GET[$idGET]))
			throw new \Exception('No question id in url');
		return $_GET[$idGET];
	}

	/**
	 * @param  \form\model\QuestionCredentials $qCred
	 * @return string HTML
	 */
	public function getEditHTML(\form\model\QuestionCredentials $qCred) {
		$html = $this->displayMessages();
		$html .= $this->getFormHead($qCred);
		$aCredArray = $qCred->getAnswers();

		foreach ($aCredArray as $aCred) {
			$title = $aCred->getTitle();
			$type = $aCred->getType();
			$id = $aCred->getId();
			$html .= $this->getAnswerHTML($type, $title, $id);
		}

		$html .= $this->getFormFooter(true);
		return $html;
	}

	/**
	 * @return string HTML
	 */
	public function getHTML() {
		$html = $this->displayMessages();
		$html .= $this->getFormHead();
		
		if ($this->isSubmitning()) {
			$html .= $this->getAnswersHTML();
		} else {
			for ($i=1; $i < 4 ; $i++) { 
				$html .= $this->getAnswerHTML(\form\model\AnswerType::GetName(2), "", $i, false);
			}
		}

		$html .= $this->getFormFooter();
		return $html;
	}

	/**
	 * @param  \form\model\QuestionCredentials $qCred, not required
	 * @return string HTML
	 */
	private function getFormHead(\form\model\QuestionCredentials $qCred = null) {
		if ($qCred === null)
			$qCred = $this;
		
		$stringMinLength = \form\model\QuestionCredentials::MinStringLength;
		$maxTitleLength = \form\model\QuestionCredentials::MaxTitleLength;
		$maxDescriptionLength = \form\model\QuestionCredentials::MaxDescriptionLength;
		$formLink;
		$legendText;
		$id = $this->getFormId();
		$title = $qCred->getTitle();
		$description = $qCred->getDescription();
		$required = $qCred->isRequired();
		$commentText = $qCred->commentText();

		if ($qCred !== $this) {
			$formLink = $this->navigationView->getEditQuestionLink($id, $this->getQuestionId());
			$legendText = "Edit Question";
		} else {
			$formLink = $this->navigationView->getAddQuestionLink($id);
			$legendText = "Add a Question";
		}

		$html = "
		<div class='row'>
				<div class='col-xs-12 col-sm-10 col-lg-8'>
			<form role='form' action='$formLink' method='post' enctype='multipart/form-data'>
				<fieldset>
					<legend>$legendText</legend>";

		$html .= $this->getTitleTag($stringMinLength, $maxTitleLength, $title);
		$html .= $this->getDescriptionTag($stringMinLength, $maxDescriptionLength, true, $description);	
		$html .= $this->getRequiredHTML($required);
		$html .= $this->getCommentTextHTML($commentText);
		$html .= "
				</fieldset>
				<fieldset>";

		if ($qCred !== $this) {
			$html .= "<legend>Edit Answers</legend>";
		} else {
			$html .= "<legend>Answers</legend>";
		}
			
		return $html;
	}

	/**
	 * @param  boolean $edit
	 * @return string HTML      
	 */
	private function getFormFooter($edit = false) {
		$submitText;
		if ($edit) {
			$submitText = "Save Changes";
		} else {
			$submitText = "Add Question";
		}	
		$formLink = $this->navigationView->getGoToManageFormLink($this->getFormId());
		return "	
				</fieldset>
				<input type='submit' value='$submitText' class='btn btn-success'>
				<a href='$formLink' class='btn btn-warning'>Cancel</a>
			</form>
			</div>
			</div>";
	}

	/**
	 * @return string HTML
	 */
	private function getAnswersHTML() {
		$html = "";
		$values = $this->getAnswersValue();

		$i = 1;
		foreach ($values as $key => $value) {
			$type;
			$title;
			if (count($value) > 1) {
				$type = \form\model\AnswerType::GetName(1);
				$title = \common\Filter::sanitizeString($value[0]);
			} else {
				$type = \form\model\AnswerType::GetName(2);
				$title = \common\Filter::sanitizeString($value[0]);
			}
			$html .= $this->getAnswerHTML($type, $title, $key);
			$i++;
		}

		return $html;
	}

	/**
	 * @param  string  $type     
	 * @param  string  $title    
	 * @param  int  $i       
	 * @param  boolean $validate 
	 * @return string HTML
	 */
	private function getAnswerHTML($type, $title, $i, $validate = true) {
		$html;
		$at = self::$answerTypePOST;
		$as = self::$answerTitlePOST;
		$minLength = \form\model\AnswerCredentials::MinStringLength;
		$maxLength = \form\model\AnswerCredentials::MaxStringLength;
		$checked = "";
		$html = "<div class='form-group'>";
		if ($validate) {
			if ($type == \form\model\AnswerType::GetName(1))
				$checked = "checked";
			try {
				\form\model\AnswerCredentials::createBasic($title, $type, $i);							
			} catch (\form\model\exception\AnswerLength $e) {
				$maxLength = $e->getMaxLength();
				$minLength = $e->getMinLength();
				$html = "<div class='form-group has-error'>
						 <label for='$as$i' class='control-label'>Accepted length: $minLength-$maxLength characters</label>";	
			}
		}
		$validationData = "data-validation='length' data-validation-length='$minLength-$maxLength'";
		
		$html .= "	<label for='$as$i' class='control-label'>$i</label>
					<div>
					   	<input type='text' value='$title' class='form-control' placeholder='Answer' maxlength='$maxLength' id='$as$i' name='$as$i' $validationData>
					</div>
					<div class='checkbox'>
        				<label>
          					<input type='checkbox' id='$at$i' name='$at$i' $checked> Text input
        				</label>
     				</div>
  				</div>";
    	return $html;
	}

	/**
	 * @param  bool $required
	 * @return string HTML
	 */
	private function getRequiredHTML($required) {
		$checked = "";
		if ($required) {
			$checked = "checked";
		}
		$id = self::$requiredPOST;
		return "
			<label class='checkbox-inline' for='$id'>
  				<input type='checkbox' id='$id' name='$id' $checked>
  				Required
			</label>";
	}

	/**
	 * @param  bool $check
	 * @return string HTML
	 */
	private function getCommentTextHTML($check) {
		$checked = "";
		if ($check) {
			$checked = "checked";
		}
		$id = self::$commentTextPOST;
		return "
			<label class='checkbox-inline' for='$id'>
  				<input type='checkbox' id='$id' name='$id' $checked>
  				Optional comment input
			</label>";
	}

	/**
	 * Looks after answer title and type in post.
	 * @return array, key: answer id, value: array(title, [type text or none])
	 */
	private function getAnswersValue() {
		$at = self::$answerTypePOST;
		$as = self::$answerTitlePOST;
		$values = array();

		$asExp = '/^' . $as . '(\d*)$/';
		$atExp = '/^' . $at . '(\d*)$/';
		
		foreach($_POST as $key => $val) {
			//as(Answer type(string) will always be first, if no client side html editing have happend)
    		if (preg_match($asExp, $key) || preg_match($atExp, $key)) {
    			$key = (int)preg_replace('/\D/', '', $key);//Get the key (answer id)
        		if (array_key_exists($key, $values)) {
        			$values[$key][] = $val;
        		} else {
        			$values[$key] = array($val);
        		}
    		}
		}
		return $values;
	}

	/**
	 * @return boolean
	 */
	private function isRequired() {
		if (isset($_POST[self::$requiredPOST]))
			return true;
		else
			return false;
	}

	/**
	 * @return boolean
	 */
	private function commentText() {
		if (isset($_POST[self::$commentTextPOST]))
			return true;
		else
			return false;
	}

	/**
	 * Observer implementation
	 */

	public function saveOk($fId = null, $qId = null) {
		$this->saveMessage(1301);
		$this->navigationView->goToManageForm($this->getFormId());
		exit();//Exit script
	}

	public function saveFailed($fId = null, $qId = null) {
		$this->saveMessage(1302);
	}

	public function getFailed($fId = null, $qId = null) {
		$this->saveMessage(1303);
		$this->navigationView->goToHome();
		exit();//Exit script
	}
}