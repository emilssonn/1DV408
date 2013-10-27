<?php

namespace form\controller;

require_once("./src/form/model/Form.php");
require_once("./src/form/model/ManageQuestion.php");
require_once("./src/common/controller/IController.php");

class CreateQuestion implements \common\controller\IController {

	private $navigationView;

	private $createQuestionView;

	private $manageQuestion;

	public function __construct(\application\view\Navigation $navigationView, 
								\authorization\model\UserCredentials $user) {
		$this->navigationView = $navigationView;
		$this->createQuestionView = new \form\view\CreateQuestion($this->navigationView);
		$this->manageQuestion = new \form\model\ManageQuestion($user, $this->createQuestionView);
	}

	public function run() {
		try {
			$formId = $this->createQuestionView->getFormId();
			$this->manageQuestion->userOwnsForm($formId);
			if ($this->createQuestionView->isSaving() ) {
				try {
					$questionCred = $this->createQuestionView->getQuestionCredentials();
					$this->manageQuestion->saveNewQuestion($questionCred, $formId);
				} catch (\Exception $e) {
					return $this->createQuestionView->getHTML();
				}
			} else if($this->navigationView->editQuestion()) {
				try {//@todo spara ändrat formulär
					$qId = $this->createQuestionView->getQuestionId();
					$this->manageQuestion->questionBelongsToForm($formId, $qId);
					$question = $this->manageQuestion->getQuestion($qId);
					return $this->createQuestionView->getEditHTML($question);
				} catch (\Exception $e) {
					$this->navigationView->goToEditForm($formId);
				}
			} else {
				return $this->createQuestionView->getHTML();
			}
		} catch (\Exception $e) {
			$this->navigationView->goToHome();
		}
		
	}
}