<?php

namespace form\controller;

require_once("./src/common/controller/IController.php");
require_once("./src/form/view/ListForms.php");

class ListForms implements \common\controller\IController {

	private $navigationView;

	private $listFormsView;

	private $user;

	private $manageForm;

	public function __construct(\authorization\model\UserCredentials $user,
								\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
		$this->listFormsView = new \form\view\ListForms($this->navigationView);
		$this->user = $user;
		$this->manageForm = new \form\model\ManageForm($this->user, $this->listFormsView);
	}

	public function run() {
		if ($this->navigationView->listMyForms()) {
			$forms = $this->manageForm->getFormsByUser();
			return $this->listFormsView->getHTML($forms, true);
		} else if ($this->navigationView->listMySubmittedForms()){
			$forms = $this->manageForm->getFormsSubmittedByUser();
			return $this->listFormsView->getSubmittedFormsHTML($forms);

		} else {
			$forms = $this->manageForm->getActiveForms();
			return $this->listFormsView->getHTML($forms);
		}
	}
}