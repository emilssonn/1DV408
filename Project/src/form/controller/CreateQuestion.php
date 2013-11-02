<?php

namespace form\controller;

require_once("./src/form/model/Form.php");
require_once("./src/form/model/ManageQuestion.php");
require_once("./src/common/controller/IController.php");

/**
 * @author Peter Emilsson
 * Class responsible for creating and editing questions
 */
class CreateQuestion implements \common\controller\IController {

	/**
	 * @var \application\view\Navigation
	 */
	private $navigationView;

	/**
	 * @var \form\view\CreateQuestion
	 */
	private $createQuestionView;

	/**
	 * @var \form\model\ManageQuestion
	 */
	private $manageQuestion;

	/**
	 * @var form\model\ManageForm
	 */
	private $manageForm;

	/**
	 * @var \user\model\UserCredentials
	 */
	private $user;

	/**
	 * @param \application\view\Navigation $navigationView 
	 * @param \user\model\UserCredentials  $user          
	 */
	public function __construct(\application\view\Navigation $navigationView, 
								\user\model\UserCredentials $user) {
		$this->navigationView = $navigationView;
		$this->createQuestionView = new \form\view\CreateQuestion($this->navigationView);
		$this->manageQuestion = new \form\model\ManageQuestion($user, $this->createQuestionView);
		$this->manageForm = new \form\model\ManageForm($user, $this->createQuestionView);
		$this->user = $user;
	}

	/**
	 * @return string HTML
	 */
	public function run() {
		try {
			$formId = $this->createQuestionView->getFormId();
			$this->manageForm->userOwnsForm($formId);
			if ($this->createQuestionView->isSubmitning() ) {
				try {
					$questionCred = $this->createQuestionView->getQuestionCredentials();
					$this->manageQuestion->saveQuestion($questionCred, $formId);
				} catch (\form\model\exception\TitleLength $e) {
					$this->createQuestionView->titleError($e->getMinLength(), $e->getMaxLength());

				} catch (\form\model\exception\DescriptionLength $e) {
					$this->createQuestionView->descriptionError($e->getMinLength(), $e->getMaxLength());

				} catch (\Exception $e) {
					
				}
				return $this->createQuestionView->getHTML();
			} else if($this->navigationView->editQuestion()) {
				try {
					$qId = $this->createQuestionView->getQuestionId();
					$this->manageQuestion->questionBelongsToForm($formId, $qId);
					$question = $this->manageQuestion->getQuestion($qId);
					return $this->createQuestionView->getEditHTML($question);	
				} catch (\Exception $e) {
					$this->navigationView->goToEditForm($formId);
				}
			} else {
				return $this->createQuestionView->getHTML();
			}
		} catch (\Exception $e) {
			$this->deleteView->failedToVerify();
		}
	}
}