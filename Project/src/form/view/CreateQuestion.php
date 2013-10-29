<?php

namespace form\view;

require_once("./src/common/Filter.php");
require_once("./src/form/model/FormObserver.php");
require_once("./src/form/model/QuestionCredentials.php");
require_once("./src/form/model/AnswerCredentials.php");

class CreateQuestion implements \form\model\FormObserver {

	private $navigationView;

	private $message = array();

	private static $questionTitlePOST = "View::Question::Title";

	private static $questionDescriptionPOST = "View::Question::Description";

	private static $answerTypePOST = "at";

	private static $answerTitlePOST = "as"; 

	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
	}

	/**
	 * @return boolean
	 */
	public function isSaving() {
		return strtolower($_SERVER['REQUEST_METHOD']) == "post";
	}

	public function getQuestionCredentials() {
		$title = $this->getTitle();
		$description = $this->getDescription();
		$qCred;
		$answers;

		try {
			$qId = $this->getQuestionId();
			$qCred =  \form\model\QuestionCredentials::createFull($title, $description, $qId);
			$answers = $this->getAnswers(true);
		} catch (\Exception $e) {
			$qCred =  \form\model\QuestionCredentials::createBasic($title, $description);
			$answers = $this->getAnswers();
		}
		
		$qCred->addAnswers($answers);
		return $qCred;
	}

	public function getEditHTML(\form\model\QuestionCredentials $question) {
		$html = "";

		$html .= $this->getFormHead($question);

		foreach ($question->getAnswers() as $answer) {
			$title = $answer->getTitle();
			$type = $answer->getType();
			$id = $answer->getId();
			$html .= $this->getAnswerHTML($type, $title, $id);
		}

		$html .= $this->getFormFooter(true);
		return $html;

	}

	public function getHTML() {
		$html = $this->getFormHead();
		
		if ($this->isSaving()) {
			$html .= $this->getAnswersHTML();
		} else {
			$html .= "
						<div class='input-group'>
     						<span class='input-group-addon'>
        						<select id='at1' name='at1' class='form-control' style='width: 100px;'>
        							<option disabled>Type</option>
        							<option>Radio</option>
        							<option>Text</option>
								</select>
      						</span>
      						<input type='text' class='form-control' placeholder='Value' id='as1' name='as1' >
    					</div><!-- /input-group -->

    						<div class='input-group'>
     						<span class='input-group-addon'>
        						<select id='at2' name='at2' class='form-control' style='width: 100px;'>
        							<option disabled>Type</option>
        							<option>Radio</option>
        							<option>Text</option>
								</select>
      						</span>
      						<input type='text' class='form-control' placeholder='Value' id='as2' name='as2' >
    					</div><!-- /input-group -->

    						<div class='input-group'>
     						<span class='input-group-addon'>
        						<select id='at3' name='at3' class='form-control' style='width: 100px;'>
        							<option disabled>Type</option>
        							<option>Radio</option>
        							<option>Text</option>
								</select>
      						</span>
      						<input type='text' class='form-control' placeholder='Value' id='as3' name='as3' >
    					</div><!-- /input-group -->";
		}

		$html .= $this->getFormFooter();
		return $html;
	}

	private function getAnswersHTML() {
		$html = "";
		$values = $this->getAnswersValue();

		$i = 1;
		foreach ($values as $key => $value) {
			$type = \common\Filter::sanitizeString($value[0]);
			$title = \common\Filter::sanitizeString($value[1]);
			$html .= $this->getAnswerHTML($type, $title, $key);
			$i++;
		}

		return $html;
	}

	private function getAnswersValue() {
		$at = self::$answerTypePOST;
		$as = self::$answerTitlePOST;
		$values = array();

		$asExp = '/^' . $as . '(\d*)$/';
		$atExp = '/^' . $at . '(\d*)$/';
		
		foreach($_POST as $key => $val) {
			//as(Answer type(string) will always be first, if no client side hmtl editing have happend)
    		if (preg_match($asExp, $key) || preg_match($atExp, $key)) {
    			$key = (int)preg_replace('/\D/', '', $key);
        		if (array_key_exists($key, $values)) {
        			$values[$key][] = $val;
        		} else {
        			$values[$key] = array($val);
        		}
    		}
		}

		return $values;
	}

	private function getAnswerHTML($type, $title, $i) {
		$html;
		$at = self::$answerTypePOST;
		$as = self::$answerTitlePOST;
		$selectOptions = $this->getSelectOptions($type);
		try {
			\form\model\AnswerCredentials::createBasic($title, $type, $i);
			$html = "<div class='input-group'>";				
		} catch (\Exception $e) {
			$html = "<div class='input-group has-error'>";	
		}
		$html .= "<span class='input-group-addon'>
        			<select id='$at$i' name='$at$i' class='form-control' style='width: 100px;'>
        				$selectOptions
					</select>
      			</span>
      			<input type='text' value='$title' class='form-control' placeholder='Value' id='$as$i' name='$as$i' >
    		</div><!-- /input-group -->";
    	return $html;
	}

	private function getSelectOptions($type) {
		$types = array("radio", "text");
		$html = "";
		$html .= "<option disabled>Type</option>";
		foreach ($types as $value) {
			if ($type === $value) {
				$html .= "<option selected='true'>$value</option>";
				continue;
			}
			$html .= "<option>$value</option>";
		}
		return $html;
	}

	private function getFormHead($question = null) {
		$id;
		$title;
		$description;
		$formLink;
		$legendText;
		if ($question !== null) {
			$id = $this->getFormId();
			$title = $question->getTitle();
			$description = $question->getDescription();
			$formLink = $this->navigationView->getEditQuestionLink($id, $this->getQuestionId());
			$legendText = "Edit Question";
		} else {
			$id = $this->getFormId();
			$title = $this->getTitle();
			$description = $this->getDescription();
			$formLink = $this->navigationView->getAddQuestionLink($id);
			$legendText = "Add a Question";
		}

		$html = "
			<form role='form' action='$formLink' method='post' enctype='multipart/form-data'>
				<fieldset>
					<legend>$legendText</legend>
					
					<div class='form-group'>
						<label for='titleID'>Question:</label>
						<input type='text' value='$title' name='" . self::$questionTitlePOST . "' id='titleID' class='form-control' placeholder='Question' autofocus>
					</div>

					<div class='form-group'>
						<label for='descriptionId'>Description (Optional):</label>
						<textarea name='" . self::$questionDescriptionPOST . "' id='descriptionId' class='form-control' placeholder='Description'>$description</textarea>
					</div>
				</fieldset>
				<fieldset>
					<legend>Answers</legend>";
		return $html;
	}

	private function getFormFooter($edit = false) {
		$submitText;
		if ($edit) {
			$submitText = "Save Changes";
		} else {
			$submitText = "Add Question";
		}	
		$formLink = $this->navigationView->getGoToEditFormLink($this->getFormId());
		return "	
				</fieldset>
				<input type='submit' value='$submitText' class='btn btn-success'>
				<a href='$formLink' class='btn btn-warning'>Cancel</a>
			</form>";
	}

	private function getAnswers($edit = false) {
		$answers = array();
		$values = $this->getAnswersValue();

		$i = 1;
		foreach ($values as $key => $value) {
			$type = \common\Filter::sanitizeString($value[0]);
			$title = \common\Filter::sanitizeString($value[1]);
			$answer = \form\model\AnswerCredentials::createFull($title, $type, $i, $key);
			$answers[] = $answer;
			$i++;
		}
		return $answers;
	}

	private function getTitle() {
		if (isset($_POST[self::$questionTitlePOST]))
			return \Common\Filter::sanitizeString($_POST[self::$questionTitlePOST]);
		else
			return "";
	}

	private function getDescription() {
		if (isset($_POST[self::$questionDescriptionPOST]))
			return \Common\Filter::sanitizeString($_POST[self::$questionDescriptionPOST]);
		else
			return "";
	}

	public function getFormId() {
		$idGET = $this->navigationView->getForm();
		if (empty($_GET[$idGET]))
			throw new \Exception('No form id in url');
		return $_GET[$idGET];
	}

	public function getQuestionId() {
		$idGET = $this->navigationView->getQuestion();
		if (empty($_GET[$idGET]))
			throw new \Exception('No question id in url');
		return $_GET[$idGET];
	}

	public function addFormOk(\form\model\FormCredentials $formCred) {
		
	}

	public function addFormFailed() {

	}

	public function getFormFailed() {

	}

	public function addQuestionOk() {
		$this->navigationView->goToEditForm($this->getFormId());
	}

	public function getFormOk() {
		
	}
}