<?php

namespace form\view;

require_once("./src/form/model/FormObserver.php");

class SubmittedForm implements \form\model\FormObserver {

	protected $navigationView;

	protected $form;

	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
	}


	public function isSubmitning() {
		return strtolower($_SERVER['REQUEST_METHOD']) == "post";
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

	protected function getAnswer($qId) {
		if (isset($_POST["$qId"])) {
			$answerId = $_POST["$qId"];
			return \form\model\AnswerViewCredentials::createBasic($qId, $answerId);
		} else {
			throw new \Exception();
		}
	}

	public function getFormId() {
		$idGET = $this->navigationView->getForm();
		if (empty($_GET[$idGET]))
			throw new \Exception('No form id in url');
		return $_GET[$idGET];
	}

	public function getSubmittedFormId() {
		$idGET = $this->navigationView->getShowForm();
		if (empty($_GET[$idGET]))
			throw new \Exception('No submitted form id in url');
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