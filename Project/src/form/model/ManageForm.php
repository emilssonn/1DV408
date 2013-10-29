<?php

namespace form\model;

require_once("./src/form/model/TemplateFormDAL.php");
require_once("./src/form/model/UserFormDAL.php");

class ManageForm {

	private $user;

	private $templateFormDAL;

	private $userFormDAL;

	private $formObserver;

	public function __construct(\authorization\model\UserCredentials $user,
								\form\model\FormObserver $formObserver) {
		$this->user = $user;
		$this->formObserver = $formObserver;
		$this->templateFormDAL = new \form\model\TemplateFormDAL($user);
		$this->userFormDAL =  new \form\model\UserFormDAL($user);
	}

	public function saveNewForm(\form\model\FormCredentials $formCred) {
		try {
			$dbFormCred = $this->templateFormDAL->insertForm($formCred);
			$this->formObserver->addFormOk($dbFormCred);
		} catch (\Exception $e) {
			throw $e;
		}
	}

	public function saveAnswers(\form\model\Form $form, $answerViewCredentialsArray) {
		$this->userFormDAL->insertAnsweredForm($form, $answerViewCredentialsArray);
	}

	public function updateAnswers(\form\model\SubmittedFormCredentials $submittedFormCredentials, $answers) {
		$this->userFormDAL->updateAnsweredForm($submittedFormCredentials, $answers);
		$this->formObserver->addFormOk();
	}

	public function userOwnsForm($formId) {
		$this->templateFormDAL->userOwnsForm($formId);
	}

	public function getForm($id) {
		try {
			$form = $this->templateFormDAL->getFormById($id);
			$this->formObserver->getFormOk();
			return $form;
		} catch (\Exception $e) {
			
		}
	}

	public function getFullForm($id) {
		$form = $this->templateFormDAL->getFullForm($id);
		$this->formObserver->getFormOk();
		return $form;
	}

	public function getActiveForms() {
		try {
			$forms = $this->templateFormDAL->getForms();
			return $forms;
		} catch (\Exception $e) {

		}
	}

	public function getFormsByUser() {
		$forms = $this->templateFormDAL->getForms(false);
		return $forms;
	}

	public function getFormResults($id) {
		return $this->userFormDAL->getFormResult($id);
	}

	public function getFormsSubmittedByUser() {
		return $this->userFormDAL->getSubmittedFormsByUser($this->templateFormDAL);
	}

	public function getFormResultByUser($subFormId) {
		return $this->userFormDAL->getFormResultByUser($subFormId, $this->templateFormDAL);
	}

}