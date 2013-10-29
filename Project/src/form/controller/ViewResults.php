<?php

namespace form\controller;

require_once("./src/common/controller/IController.php");
require_once("./src/form/view/ViewResults.php");

class ViewResults implements \common\controller\IController {

	private $navigationView;

	private $viewResultsView;

	private $user;

	private $manageForm;

	public function __construct(\authorization\model\UserCredentials $user,
								\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
		$this->viewResultsView = new \form\view\ViewResults($this->navigationView);
		$this->user = $user;
		$this->manageForm = new \form\model\ManageForm($this->user, $this->viewResultsView);
	}

	public function run() {
		try {
			$formId = $this->viewResultsView->getFormId();
			$this->manageForm->userOwnsForm($formId);
			$form = $this->manageForm->getFullForm($formId);
			$formResults = $this->manageForm->getFormResults($form);
			return $this->viewResultsView->getHTML($form, $formResults);
		} catch (\Exception $e) {
			$this->navigationView->goToHome();
		}
	}
}