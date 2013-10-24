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

	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
	}

	public function getHTML() {
		$id = $this->getFormId();
		$html = "
			<form action='?" . $this->navigationView->getForm() . "=$id&" . $this->navigationView->getCreateForm() . "&". $this->navigationView->getQuestion() . "' method='post' enctype='multipart/form-data' class='form-signin'>
				<fieldset>
					<legend class='form-signin-heading'>Add a question</legend>
					
					<label for='titleID'>Question:</label>
					<input type='text' name='" . self::$questionTitlePOST . "' id='titleID' class='form-control' placeholder='Title' autofocus>
					
					<label for='descriptionId'>Description (Optional):</label>
					<textarea name='" . self::$questionDescriptionPOST . "' id='descriptionId' class='form-control' placeholder='Description'></textarea>

					<h3>Answers</h3>

					<div class='input-group'>
     					<span class='input-group-addon'>
        					<select id='at1' name='at1' class='form-control' style='width: 100px;'>
        						<option disabled>Type</option>
								<option value='radio'>Radio</option>
							</select>
      					</span>
      					<input type='text' class='form-control' placeholder='Value' id='as1' name='as1' >
    				</div><!-- /input-group -->

    				<div class='input-group'>
     					<span class='input-group-addon'>
        					<select id='at2' name='at2' class='form-control' style='width: 100px;'>
        						<option disabled>Type</option>
								<option value='radio'>Radio</option>
							</select>
      					</span>
      					<input type='text' class='form-control' placeholder='Value' id='as2' name='as2' >
    				</div><!-- /input-group -->

    				<div class='input-group'>
     					<span class='input-group-addon'>
        					<select id='at3' name='at3' class='form-control' style='width: 100px;'>
        						<option disabled>Type</option>
								<option value='radio'>Radio</option>
							</select>
      					</span>
      					<input type='text' class='form-control' placeholder='Value' id='as3' name='as3' >
    				</div><!-- /input-group -->

				
					<input type='submit' value='Add Question' class='btn btn-lg btn-primary btn-block'>
				</fieldset>
			</form>";

		return $html;
	}

	/**
	 * @return boolean
	 */
	public function isCreating() {
		return strtolower($_SERVER['REQUEST_METHOD']) == "post";
	}

	public function getQuestionCredentials() {
		$title = $this->getTitle();
		$description = $this->getDescription();
		$qCred =  \form\model\QuestionCredentials::createFormBasic($title, $description);
		return $this->getAnswers($qCred);
	}

	private function getAnswers($qCred) {
		$i = 1;
		while (true) {
			try {
				if (isset($_POST['at' . $i])) {
					$type = $_POST['at' . $i];
					$title = $_POST['as' . $i];
					$answer = \form\model\AnswerCredentials::createFormBasic($title, $type, $i);
					$qCred->addAnswer($answer);
					$i++;
				} else {
					break;
				}
			} catch (\Exception $e) {
				break;
			}
		}
		return $qCred;
	}

	public function getFormId() {
		$idGET = $this->navigationView->getForm();
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
}

/*
<div class='input-group'>
     					<span class='input-group-addon'>
        					<input type='radio' name='answer' value='r0' id='r0'>
      					</span>
      					<label for='r0' class='form-control'>Passed</label>
    				</div><!-- /input-group -->

    				<div class='input-group'>
     					<span class='input-group-addon'>
        					<input type='radio' name='answer' value='r1' id='r1'>
      					</span>
      					<label for='1' class='form-control'>Failed</label>
    				</div><!-- /input-group -->

    				<div class='input-group'>
     					<span class='input-group-addon'>
        					<input type='radio' name='answer' value='2'>
      					</span>
      					<input type='text' class='form-control' placeholder='Other'>
    				</div><!-- /input-group -->

    				<label for='optionalText'></label>
    				<textarea class='form-control' rows='3' name='optionalText' id='optionalText'></textarea>
 */