<?php

namespace form\model;

require_once("./src/form/model/TemplateFormDAL.php");
require_once("./src/form/model/UserFormDAL.php");

/**
 * @author Peter Emilsson
 * Handles all form actions, template and answering
 */
class ManageForm {

	/**
	 * @var \user\model\UserCredentials
	 */
	private $user;

	/**
	 * @var \form\model\TemplateFormDAL
	 */
	private $templateFormDAL;

	/**
	 * @var \form\model\UserFormDAL
	 */
	private $userFormDAL;

	/**
	 * View that implements \form\model\FormObserver
	 * @var \form\model\FormObserver
	 */
	private $formObserver;

	/**
	 * @param \user\model\UserCredentials $user        
	 * @param \form\model\FormObserver    $formObserver 
	 */
	public function __construct(\user\model\UserCredentials $user,
								\form\model\FormObserver $formObserver) {
		$this->user = $user;
		$this->formObserver = $formObserver;
		$this->templateFormDAL = new \form\model\TemplateFormDAL($user);
		$this->userFormDAL =  new \form\model\UserFormDAL($user);
	}

	/**
	 * Save and new form or update an existing form
	 * depending if a id is set
	 * @param  \form\model\Form $form 
	 */
	public function saveForm(\form\model\Form $form) {
		try {
			if ($form->getId() === null) {
				$formId = $this->templateFormDAL->insertForm($form);	
				$this->formObserver->saveOk($formId);
			} else {
				$this->templateFormDAL->updateForm($form);	
				$this->formObserver->saveOk($form->getId());
			}
		} catch (\Exception $e) {
			$this->formObserver->saveFailed();
		}
	}

	/**
	 * @param  \form\model\Form $form         
	 * @param  array of \form\model\AnswerViewCredentials 	$answerViewCredentialsArray 
	 */
	public function saveAnswers(\form\model\Form $form, $answerViewCredentialsArray) {
		try {		
			$userFormId = $this->userFormDAL->insertAnsweredForm($form, $answerViewCredentialsArray);
			$this->formObserver->saveOk($userFormId);
		} catch (\Exception $e) {
			$this->formObserver->saveFailed();
		}
	}

	/**
	 * @param  \form\model\SubmittedFormCredentials 		$submittedFormCredentials 
	 * @param  array of \form\model\QuestionViewCredentials $qViewCredArray                
	 */
	public function updateAnswers(\form\model\SubmittedFormCredentials $submittedFormCredentials, $qViewCredArray) {
		try {
			$userFormId = $this->userFormDAL->updateAnsweredForm($submittedFormCredentials, $qViewCredArray);
			$this->formObserver->saveOk($userFormId);
		} catch (\Exception $e) {
			$this->formObserver->saveFailed();
		}
	}

	/**
	 * @param  int $formId
	 */
	public function deleteForm($formId) {
		try {
			$this->templateFormDAL->deleteForm($formId);	
			$this->formObserver->deleteOk();
		} catch (\Exception $e) {
			$this->formObserver->deleteFailed();
		}
	}

	/**
	 * @param  int $formId      
	 */
	public function userOwnsForm($formId) {
		try {
			return $this->templateFormDAL->userOwnsForm($formId);	
		} catch (\Exception $e) {
			$this->formObserver->failedToVerify();
		}
	}

	/**
	 * @param  int  $formId    
	 * @param  boolean $manage 
	 * @return \form\model\Form
	 */
	public function getFullForm($formId, $manage = false) {
		try {
			$form = $this->templateFormDAL->getFullForm($formId);
			if (!$manage) {
				if (!$form->isActive())
					$this->formObserver->notActive();
				else if (!$form->isPublished() && $form->getAuthor()->getId() !== $this->user->getId())
					$this->formObserver->notPublic();
			}
			return $form;
		} catch (\Exception $e) {
			$this->formObserver->getFailed();
		}
	}

	/**
	 * @param  int $formId 
	 * @return \form\model\Form
	 */
	public function getBasicForm($formId) {
		try {
			$form = $this->templateFormDAL->getFormById($formId);
			return $form;
		} catch (\Exception $e) {
			$this->formObserver->getFailed();
		}
	}

	/**
	 * @return \form\model\FormCollection
	 */
	public function getActiveForms() {
		try {
			$forms = $this->templateFormDAL->getForms();
			return $forms;
		} catch (\Exception $e) {
			$this->formObserver->getFailed();
		}
	}

	/**
	 * @return \form\model\FormCollection
	 */
	public function getFormsByUser() {
		try {
			$forms = $this->templateFormDAL->getForms(false);
			return $forms;
		} catch (\Exception $e) {
			$this->formObserver->getFailed();
		}
	}

	/**
	 * @return array of \form\model\SubmittedFormCredentials
	 */
	public function getFormsSubmittedByUser() {
		try {
			return $this->userFormDAL->getSubmittedFormsByUser($this->templateFormDAL);
		} catch (\Exception $e) {
			$this->formObserver->getFailed();
		}	
	}

	/**
	 * @param  \form\model\Form $form
	 * @return array of \form\model\QuestionResultCredentials
	 */
	public function getFormResults(\form\model\Form $form) {
		try {
			return $this->userFormDAL->getFormResult($form);
		} catch (\Exception $e) {
			$this->formObserver->getFailed();
		}
	}

	/**
	 * @param  int $subFormId 
	 * @return \form\model\SubmittedFormCredentials
	 */
	public function getFormResultByUser($subFormId) {
		try {
			return $this->userFormDAL->getFormResultByUser($subFormId, $this->templateFormDAL);
		} catch (\Exception $e) {
			$this->formObserver->getFailed();
		}
	}

	/**
	 * @todo  it is possible to publish a form and then delete all question and it will still be public
	 * @param  int $formId        
	 */
	public function publishForm($formId) {
		try {
			if (!$this->templateFormDAL->formHasQuestions($formId)) {
				$this->formObserver->noQuestions($formId);
			} else {
				$this->templateFormDAL->publishForm($formId);
				$this->formObserver->publishOk($formId);
			}
		} catch (\Exception $e) {
			$this->formObserver->publishFailed($formId);
		}
	}
}