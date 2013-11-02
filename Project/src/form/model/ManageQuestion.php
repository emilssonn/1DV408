<?php

namespace form\model;

require_once("./src/form/model/TemplateFormDAL.php");
require_once("./src/form/model/Form.php");

/**
 * @author Peter Emilsson
 * Handles all question actions
 */
class ManageQuestion {

	/**
	 * @var \user\model\UserCredentials
	 */
	private $user;

	/**
	 * View that implements \form\model\FormObserver
	 * @var \form\model\FormObserver
	 */
	private $formObserver;

	/**
	 * @var \form\model\TemplateQuestionDAL
	 */
	private $questionDAL;

	/**
	 * @param \user\model\UserCredentials $user        
	 * @param \form\model\FormObserver    $formObserver 
	 */
	public function __construct(\user\model\UserCredentials $user,
								\form\model\FormObserver $formObserver) {
		$this->user = $user;
		$this->formObserver = $formObserver;
		$this->questionDAL = new \form\model\TemplateQuestionDAL();
	}

	/**
	 * Save and new question or update an existing question
	 * depending if a id is set
	 * @param  \form\model\QuestionCredentials  $questionCred 
	 * @param  int                      		$formId       
	 */
	public function saveQuestion(\form\model\QuestionCredentials $questionCred, $formId) {
		try {
			if ($questionCred->getId() === null) {
				$this->questionDAL->insertQuestion($questionCred, $formId);
			} else {
				$this->questionDAL->updateQuestion($questionCred, $formId);
			}
			$this->formObserver->saveOk();
		} catch (\Exception $e) {
			$this->formObserver->saveFailed();
		}
	}

	/**
	 * @param  int $fId
	 * @param  int $qId 
	 * @return bool
	 */
	public function questionBelongsToForm($fId, $qId) {
		if ($this->questionDAL->questionBelongsToForm($fId, $qId))
			return true;
		else
			$this->formObserver->failedToVerify();
	}

	/**
	 * @param  int $qId
	 * @return \form\model\QuestionCredentials
	 */
	public function getQuestion($qId) {
		try {
			return $this->questionDAL->getQuestionById($qId);
		} catch (\Exception $e) {
			$this->formObserver->getFailed();
		}
	}

	/**
	 * @param  int $fId 
	 * @param  int $qId 
	 */
	public function deleteQuestion($fId, $qId) {
		try {
			$this->questionDAL->deleteQuestion($qId);
			$this->formObserver->deleteOk($fId, $qId);
		} catch (\Exception $e) {
			$this->formObserver->deleteFailed($fId, $qId);
		}
	}
}