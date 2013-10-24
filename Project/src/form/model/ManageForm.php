<?php

namespace form\model;

require_once("./src/form/model/FormDAL.php");
require_once("./src/form/model/Form.php");

class ManageForm {

	private $user;

	private $formDAL;

	private $formObserver;

	public function __construct(\authorization\model\UserCredentials $user,
								\form\model\FormObserver $formObserver) {
		$this->user = $user;
		$this->formObserver = $formObserver;
		$this->formDAL = new \form\model\FormDAL($user);
	}

	public function saveNewForm(\form\model\FormCredentials $formCred) {
		try {
			if (!$this->formDAL->formExists($formCred)) {
				$dbFormCred = $this->formDAL->insertForm($formCred);
				$this->formObserver->addFormOk($dbFormCred);
			} else {
				
				throw new \Exception();
			}
		} catch (\Exception $e) {
			throw $e;
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

}