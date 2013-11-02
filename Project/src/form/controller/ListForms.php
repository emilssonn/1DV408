<?php

namespace form\controller;

require_once("./src/common/controller/IController.php");
require_once("./src/form/view/ListForms.php");

/**
 * @author Peter Emilsson
 * Responsible for listing forms
 */
class ListForms implements \common\controller\IController {

	/**
	 * @var \application\view\Navigation
	 */
	private $navigationView;

	/**
	 * @var \form\view\ListForms
	 */
	private $listFormsView;

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
		$this->listFormsView = new \form\view\ListForms($this->navigationView);
		$this->user = $user;
		$this->manageForm = new \form\model\ManageForm($this->user, $this->listFormsView);
	}

	/**
	 * @return string HTML
	 */
	public function run() {
		try {
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
		} catch (\Exception $e) {
			//Should never happend
		}
	}
}