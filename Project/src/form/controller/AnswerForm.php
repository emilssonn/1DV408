<?php

namespace form\controller;

require_once("./src/common/controller/IController.php");
require_once("./src/form/view/AnswerForm.php");

/**
 * @author Peter Emilsson
 * Class for answering a form, view result and changing answers is done by ManageSubmittedForm controller
 */
class AnswerForm implements \common\controller\IController {

	/**
	 * @var \application\view\Navigation
	 */
	private $navigationView;

	/**
	 * @var \form\view\AnswerForm
	 */
	private $answerFormView;

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
		$this->answerFormView = new \form\view\AnswerForm($navigationView);
		$this->user = $user;
		$this->manageForm = new \form\model\ManageForm($this->user, $this->answerFormView);
	}

	/**
	 * @return string HTML
	 */
	public function run() {
		try {
			$formId = $this->answerFormView->getFormId();
			$form = $this->manageForm->getFullForm($formId);

			if ($this->answerFormView->isSubmitning()) {  
				try {
					$answers = $this->answerFormView->getAnswers($form);
					$this->manageForm->saveAnswers($form, $answers);
				} catch (\Exception $e) {
					//Do nothing
				} 
				return $this->answerFormView->getHTML($form);
			} else {
				return $this->answerFormView->getHTML($form);
			}
		} catch (\Exception $e) {
			$this->answerFormView->getFailed();
		}
	}
}