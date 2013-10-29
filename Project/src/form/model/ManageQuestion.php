<?php

namespace form\model;

require_once("./src/form/model/TemplateFormDAL.php");
require_once("./src/form/model/Form.php");

class ManageQuestion {

	private $user;

	private $formDAL;

	private $formObserver;

	private $questionDAL;

	public function __construct(\authorization\model\UserCredentials $user,
								\form\model\FormObserver $formObserver) {
		$this->user = $user;
		$this->formObserver = $formObserver;
		$this->questionDAL = new \form\model\TemplateQuestionDAL();
	}

	public function saveQuestion(\form\model\QuestionCredentials $questionCred, $formId) {
		try {
			if ($questionCred->getId() === null) {
				$this->questionDAL->insertQuestion($questionCred, $formId);
				$this->formObserver->addQuestionOk();
			} else {
				$this->questionDAL->updateQuestion($questionCred, $formId);
				$this->formObserver->addQuestionOk();
			}
		} catch (\Exception $e) {
			throw $e;
		}
	}

	public function questionBelongsToForm($fId, $qId) {

	}

	public function getQuestion($qId) {
		return $this->questionDAL->getQuestionById($qId);
	}

}