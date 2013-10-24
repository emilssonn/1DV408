<?php

namespace form\controller;

require_once("./src/form/model/Form.php");
require_once("./src/form/model/ManageQuestion.php");

class CreateQuestion {

	private $createQuestionView;

	private $manageQuestion;

	private $form;

	public function __construct(\application\view\Navigation $navigationView, 
								\authorization\model\UserCredentials $user) {
		$this->createQuestionView = new \form\view\CreateQuestion($navigationView);
		$this->manageQuestion = new \form\model\ManageQuestion($user, $this->createQuestionView);
	}

	public function run() {
		if ($this->createQuestionView->isCreating() ) {
			try {
				$questionCred = $this->createQuestionView->getQuestionCredentials();
				$formId = $this->createQuestionView->getFormId();
				$this->manageQuestion->saveNewQuestion($questionCred, $formId);
			} catch (\Exception $e) {
				
			}
		} else {
			return $this->createQuestionView->getHTML();
		}
	}
}