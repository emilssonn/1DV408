<?php

namespace form\controller;

require_once("./src/common/controller/IController.php");
require_once("./src/form/view/ManageSubmittedForm.php");

/**
 * @author Peter Emilsson
 * Class for view a form result and editing a submitted form
 */
class ManageSubmittedForm implements \common\controller\IController {
	
	/**
	 * @var \application\view\Navigation
	 */
	private $navigationView;

	/**
	 * @var \form\view\ManageSubmittedForm
	 */
	private $manageSubmittedFormView;

	/**
	 * @var \user\model\UserCredentials
	 */
	private $user;

	/**
	 * @var \form\model\ManageForm
	 */
	private $manageForm;

	/**
	 * @param \user\model\UserCredentials  $user         
	 * @param \application\view\Navigation $navigationView 
	 */
	public function __construct(\user\model\UserCredentials $user,
								\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
		$this->manageSubmittedFormView = new \form\view\ManageSubmittedForm($navigationView);
		$this->user = $user;
		$this->manageForm = new \form\model\ManageForm($this->user, $this->manageSubmittedFormView);
	}

	/**
	 * @return string HTML
	 */
	public function run() {
		try {
			$formId = $this->manageSubmittedFormView->getFormId();
			$form = $this->manageForm->getFullForm($formId, true);	
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
				if ($this->manageSubmittedFormView->edit() && $form->isActive() && $form->isPublished()) {
					return $this->manageSubmittedFormView->getHTML($form, $submittedFormCredentials, true);
				} else {
					return $this->manageSubmittedFormView->getHTML($form, $submittedFormCredentials, false);
				}
			}		
		} catch (\Exception $e) {
			$this->manageSubmittedFormView->getFailed();
		}
	}
}