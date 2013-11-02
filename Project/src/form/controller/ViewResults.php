<?php

namespace form\controller;

require_once("./src/common/controller/IController.php");
require_once("./src/form/view/ViewResults.php");

/**
 * @author Peter Emilsson
 * Responsible for displaying combinded results for a form
 */
class ViewResults implements \common\controller\IController {

	/**
	 * @var \application\view\Navigation
	 */
	private $navigationView;

	/**
	 * @var \form\view\ViewResults
	 */
	private $viewResultsView;

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
		$this->viewResultsView = new \form\view\ViewResults($this->navigationView);
		$this->user = $user;
		$this->manageForm = new \form\model\ManageForm($this->user, $this->viewResultsView);
	}

	/**
	 * Do the the correct action
	 * @return string HTML
	 */
	public function run() {
		try {
			$formId = $this->viewResultsView->getFormId();
			$this->manageForm->userOwnsForm($formId);
			$form = $this->manageForm->getFullForm($formId);
			$formResults = $this->manageForm->getFormResults($form);
			return $this->viewResultsView->getHTML($form, $formResults);
		} catch (\Exception $e) {
			$this->viewResultsView->getFailed();
		}
	}
}