<?php

namespace form\controller;

require_once("./src/common/controller/IController.php");
require_once("./src/form/view/Delete.php");

/**
 * @author Peter Emilsson
 * Class responsible for deleting questions and forms
 */
class Delete implements \common\controller\IController {
	
	/**
	 * @var \form\view\Delete
	 */
	private $deleteView;

	/**
	 * @var \user\model\UserCredentials
	 */
	private $user;

	/**
	 * @var \form\model\ManageForm
	 */
	private $manageForm;

	/**
	 * @var \form\model\ManageQuestion
	 */
	private $manageQuestion;

	/**
	 * @var \application\view\Navigation
	 */
	private $navigationView;

	public function __construct(\user\model\UserCredentials $user,
								\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
		$this->deleteView = new \form\view\Delete($navigationView);
		$this->user = $user;
		$this->manageForm = new \form\model\ManageForm($this->user, $this->deleteView);
		$this->manageQuestion = new \form\model\ManageQuestion($this->user, $this->deleteView);
	}

	/**
	 * @return string HTML
	 */
	public function run() {
		try {	
			if ($this->deleteView->deleteQuestion()) {
				$formId = $this->deleteView->getFormId();
				$this->manageForm->userOwnsForm($formId);
				$questionId = $this->deleteView->getQuestionId();
				$this->manageQuestion->questionBelongsToForm($formId, $questionId);
				$this->manageQuestion->deleteQuestion($formId, $questionId);
			} else if ($this->deleteView->deleteForm()) {
				$formId = $this->deleteView->getFormId();
				$this->manageForm->userOwnsForm($formId);
				$this->manageForm->deleteForm($formId);
			}
		} catch (\Exception $e) {
			$this->deleteView->failedToVerify();
		}
	}
}