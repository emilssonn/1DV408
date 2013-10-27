<?php

namespace form\controller;

require_once("./src/common/controller/IController.php");
require_once("./src/form/view/AnswerForm.php");

class AnswerForm implements \common\controller\IController {

	private $navigationView;

	private $answerFormView;

	private $user;

	private $manageForm;

	public function __construct(\authorization\model\UserCredentials $user,
								\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
		$this->answerFormView = new \form\view\AnswerForm($navigationView);
		$this->user = $user;
		$this->manageForm = new \form\model\ManageForm($this->user, $this->answerFormView);
	}

	public function run() {
		try {
			$formId = $this->answerFormView->getFormId();
			$form = $this->manageForm->getFullForm($formId);

			if ($this->answerFormView->isSubmitning()) {  
				try {
					$answers = $this->answerFormView->getAnswers($form);
					$this->manageForm->saveAnswers($form, $answers);
				} catch (\Exception $e) {
					return $this->answerFormView->getHTML($form);
				}
			} else {
				return $this->answerFormView->getHTML($form);
			}
		} catch (\Exception $e) {
			$this->navigationView->goToHome();
		}
	}
}