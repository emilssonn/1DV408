<?php

namespace form\controller;

require_once("./src/common/controller/IController.php");
require_once("./src/form/view/ManageSubmittedForm.php");

class ManageSubmittedForm implements \common\controller\IController {
	
	private $navigationView;

	private $manageSubmittedFormView;

	private $user;

	private $manageForm;

	public function __construct(\authorization\model\UserCredentials $user,
								\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
		$this->manageSubmittedFormView = new \form\view\ManageSubmittedForm($navigationView);
		$this->user = $user;
		$this->manageForm = new \form\model\ManageForm($this->user, $this->manageSubmittedFormView);
	}

	public function run() {
		try {
			$formId = $this->manageSubmittedFormView->getFormId();
			$form = $this->manageForm->getFullForm($formId);	
			$subFormId = $this->manageSubmittedFormView->getSubmittedFormId();
			$submittedFormCredentials = $this->manageForm->getFormResultByUser($subFormId);

			if ($this->manageSubmittedFormView->isSubmitning() && $form->isActive()) {
				try {
					$answers = $this->manageSubmittedFormView->getAnswers($form);
					$this->manageForm->updateAnswers($submittedFormCredentials, $answers);
					$this->navigationView->goToHome();
				} catch (\Exception $e) {
					return $this->manageSubmittedFormView->getHTML($form, $submittedFormCredentials, true);
				}
			} else {
				if ($this->manageSubmittedFormView->edit() && $form->isActive()) {
					return $this->manageSubmittedFormView->getHTML($form, $submittedFormCredentials, true);
				} else {
					return $this->manageSubmittedFormView->getHTML($form, $submittedFormCredentials, false);
				}
			}		
		} catch (\Exception $e) {
			$this->navigationView->goToHome();
		}
	}
}