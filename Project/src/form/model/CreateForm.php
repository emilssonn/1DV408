<?php

namespace form\model;

require_once("./src/form/model/FormDAL.php");
require_once("./src/form/model/PersistantForm.php");
require_once("./src/form/model/Form.php");

class CreateForm {

	private $user;

	private $formDAL;

	private $formObserver;

	private $persistantForm;

	public function __construct(\authorization\model\UserCredentials $user,
								\form\model\FormObserver $formObserver) {
		$this->user = $user;
		$this->formObserver = $formObserver;
		$this->formDAL = new \form\model\FormDAL($user);
		$this->persistantForm = new \form\model\PersistantForm();
	}

	public function saveNewForm(\form\model\FormCredentials $formCred) {
		try {
			if (!$this->formDAL->formExists($formCred)) {
				$dbFormCred = $this->formDAL->insertForm($formCred);
				$this->setCreatedForm($dbFormCred);
				$this->formObserver->addFormOk($dbFormCred);
			} else {
				
				throw new \Exception();
			}
		} catch (\Exception $e) {
			throw $e;
		}
	}

	public function hasCreatedForm() {
		try {
			$this->persistantForm->load();
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function getForm($id) {
		try {
			$form = $this->formDAL->getFormById($id);
			$this->formObserver->getFormOk();
			return $form;
		} catch (\Exception $e) {
			
		}
	}

	public function getCreatedForm() {
		return $this->persistantForm->load();
	}
	
	public function clearCreatedForm() {
		$this->persistantForm->remove();
	}

	public function saveForm(\form\model\Form $form) {
		$this->persistantForm->save($form);
	}

	private function setCreatedForm(\form\model\FormCredentials $formCred) {
		$this->persistantForm->save(new \form\model\Form($formCred));
	}
}