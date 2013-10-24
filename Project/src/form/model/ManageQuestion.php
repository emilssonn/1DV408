<?php

namespace form\model;

require_once("./src/form/model/FormDAL.php");
require_once("./src/form/model/Form.php");

class ManageQuestion {

	private $user;

	private $formDAL;

	private $formObserver;

	public function __construct(\authorization\model\UserCredentials $user,
								\form\model\FormObserver $formObserver) {
		$this->user = $user;
		$this->formObserver = $formObserver;
		$this->formDAL = new \form\model\FormDAL($user);
	}

	public function saveNewQuestion(\form\model\QuestionCredentials $questionCred, $formId) {
		try {
			
			$this->formDAL->insertQuestion($questionCred, $formId);
			$this->formObserver->addQuestionOk();
		} catch (\Exception $e) {
			throw $e;
		}
	}

}