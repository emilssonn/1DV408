<?php

namespace form\controller;

require_once("./src/form/model/ManageForm.php");
require_once("./src/form/view/CreateForm.php");
require_once("./src/form/view/CreateQuestion.php");
require_once("./src/form/controller/CreateQuestion.php");
require_once("./src/common/controller/IController.php");

/**
 * @author Peter Emilsson
 * Class creating, editing and manage a form
 * @todo This class should be split up in 2, one for creating/editing and one for displaying/publishing/deleteing
 */
class CreateForm implements \common\controller\IController {

	/**
	 * @var \form\view\CreateForm
	 */
	private $createFormView;

	/**
	 * @var \user\model\UserCredentials
	 */
	private $user;

	/**
	 * @var \form\model\ManageForm
	 */
	private $manageForm;

	/**
	 * @var \application\view\Navigation
	 */
	private $navigationView;

	/**
	 * @param \user\model\UserCredentials  $user          
	 * @param \application\view\Navigation $navigationView 
	 */
	public function __construct(\user\model\UserCredentials $user,
								\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
		$this->createFormView = new \form\view\CreateForm($this->navigationView);
		$this->user = $user;
		$this->manageForm = new \form\model\ManageForm($this->user, $this->createFormView);
	}

	/**
	 * @return string HTML
	 */
	public function run() {
		if ($this->createFormView->isSubmitning() ) {
			return $this->isSubmitting();
			
		} else if ($this->navigationView->manageForm()) {
			try {
				$formId = $this->createFormView->getFormId();
				$this->manageForm->userOwnsForm($formId);
				$form = $this->manageForm->getFullForm($formId, true);
				return $this->createFormView->getFixedHTML($form);
			} catch (\Exception $e) {
				$this->createFormView->failedToVerify();
			}	
		} else if ($this->navigationView->editForm()) {
			$formId = $this->createFormView->getFormId();
			$this->manageForm->userOwnsForm($formId);
			$form = $this->manageForm->getBasicForm($formId);
			return $this->createFormView->getHTML($form);
		} else {
			return $this->createFormView->getHTML();
		}
	}

	/**
	 * @return string HTML
	 */
	private function isSubmitting() {
		try {
			if ($this->navigationView->publishForm()) {
				$formId = $this->createFormView->getFormId();
				$this->manageForm->userOwnsForm($formId);
				$this->manageForm->publishForm($formId);
			} else {
				$form = $this->createFormView->getForm();
				if ($form->getId() !== null)
					$this->manageForm->userOwnsForm($form->getId());
				$this->manageForm->saveForm($form);
			}
		} catch (\form\model\exception\TitleLength $e) {
			$this->createFormView->titleError($e->getMinLength(), $e->getMaxLength());

		} catch (\form\model\exception\DescriptionLength $e) {
			$this->createFormView->descriptionError($e->getMinLength(), $e->getMaxLength());
		
		} catch (\form\model\exception\DateError $e) {
			$this->createFormView->dateError();

		} catch (\Exception $e) {
			//Do nothing here
		}
		return $this->createFormView->getHTML();
	}
}