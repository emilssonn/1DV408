<?php

namespace form\controller;

require_once("./src/form/model/ManageForm.php");
require_once("./src/form/view/CreateQuestion.php");
require_once("./src/form/controller/CreateQuestion.php");
require_once("./src/common/controller/IController.php");

class CreateForm implements \common\controller\IController {

	private $createFormView;

	private $user;

	private $manageForm;

	public function __construct(\authorization\model\UserCredentials $user,
								\application\view\Navigation $navigationView) {
		$this->createFormView = new \form\view\CreateForm($navigationView);
		$this->user = $user;
		$this->manageForm = new \form\model\ManageForm($this->user, $this->createFormView);
	}

	public function run() {
		if ($this->createFormView->isCreating() ) {
			try {
				$formCred = $this->createFormView->getFormCredentials();
				$this->manageForm->saveNewForm($formCred);
				return $this->createFormView->getHTML();
			} catch (\Exception $e) {
				$this->createFormView->addFormFailed();
				return $this->createFormView->getHTML();
			}
		} else if ($this->createFormView->isEditing()) {
			try {
				$formId = $this->createFormView->getFormId();
				$form = $this->manageForm->getFullForm($formId);
				return $this->createFormView->getHTML($form);
			} catch (\Exception $e) {
				
			}	
		} else {
			return $this->createFormView->getHTML();
		}
	}
}