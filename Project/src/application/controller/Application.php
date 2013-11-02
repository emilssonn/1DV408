<?php

namespace application\controller;

require_once("./src/user/authorization/view/Login.php");
require_once("./src/application/view/Application.php");
require_once("./src/user/authorization/controller/Login.php");
require_once("./src/application/view/Navigation.php");
require_once("./src/user/register/view/Register.php");
require_once("./src/user/register/controller/Register.php");
require_once("./src/home/controller/Home.php");
require_once("./src/form/controller/CreateForm.php");
require_once("./src/form/controller/ListForms.php");
require_once("./src/common/controller/IController.php");
require_once("./src/form/controller/AnswerForm.php");
require_once("./src/form/controller/ViewResults.php");
require_once("./src/form/controller/ManageSubmittedForm.php");
require_once("./src/form/controller/Delete.php");

/**
 * @author Peter Emilsson
 */
class Application implements \common\controller\IController {

	/**
	 * @var user\authorization\view\Login
	 */
	private $loginView;

	/**
	 * @var user\authorization\controller\Login
	 */
	private $loginController;

	/**
	 * @var application\view\Application
	 */
	private $applicationView;

	/**
	 * @var application\view\Navigation
	 */
	private $navigationView;

	/**
	 * @param common\view\PageView $pageView
	 */
	public function __construct(\common\view\PageView $pageView) {

		$this->navigationView = new \application\view\Navigation();
		$this->loginView = new \user\authorization\view\Login($this->navigationView);	
		$this->applicationView = new \application\view\Application($this->navigationView, $this->loginView, $pageView);	

		$this->loginController = new \user\authorization\controller\Login(
														$this->loginView, 
														$this->navigationView);
	}

	/**
	 * @return string HTML
	 */
	public function run() {
		$this->loginController->run();
	
		if ($this->loginController->isLoggedIn()) {
			$loggedInUserCredentials = $this->loginController->getLoggedInUser();

			if ($this->navigationView->userHome()) {
				return $this->home($loggedInUserCredentials);

			} else if ($this->navigationView->addQuestion() ||
						$this->navigationView->editQuestion()) {
				return $this->addFormQuestion($loggedInUserCredentials);

			} else if ($this->navigationView->createForm() ||
						$this->navigationView->manageForm() ||
						$this->navigationView->editForm()) {		
				return $this->createForm($loggedInUserCredentials);

			} else if ($this->navigationView->listForms()) {
				return $this->displayForms($loggedInUserCredentials);

			} else if ($this->navigationView->answerForm()) {
				return $this->answerForm($loggedInUserCredentials);

			} else if ($this->navigationView->showMySubmittedForm()) {
				return $this->showMySubmittedForm($loggedInUserCredentials);

			} else if ($this->navigationView->viewResults()) {
				return $this->viewResults($loggedInUserCredentials);

			} else if ($this->navigationView->delete()) {
				$this->delete($loggedInUserCredentials);
			}

			return $this->home($loggedInUserCredentials);

		} else if ($this->navigationView->wantsToRegister()) {
			return $this->register();
		} else {
			return $this->applicationView->getLoggedOutPage();
		}
	}

	private function register() {
		$registerView = new \user\register\view\Register($this->navigationView);
		$registerController = new \user\register\controller\Register($registerView, $this->loginView);
		$registerController->run();
		
		if ($registerController->wasRegSuccessfull()) {
			return $this->applicationView->getLoggedOutPage();
		} else {
			return $this->applicationView->getRegisterPage($registerView);
		}
	}

	private function home(\user\model\UserCredentials $user) {
		$homeController = new \home\controller\Home($user, $this->navigationView);
		$html = $homeController->run();
		return $this->applicationView->getHomePage($html);
	}

	private function createForm(\user\model\UserCredentials $user) {
		$createFormController = new \form\controller\CreateForm($user, $this->navigationView);
		$html = $createFormController->run();
		return $this->applicationView->getCreateFormPage($html);
	}

	private function addFormQuestion(\user\model\UserCredentials $user) {
		$createQuestionController = new \form\controller\CreateQuestion($this->navigationView, $user);
		$html = $createQuestionController->run();
		return $this->applicationView->getCreateQuestionPage($html);
	}

	private function answerForm(\user\model\UserCredentials $user) {
		$answerFormController = new \form\controller\AnswerForm($user, $this->navigationView);
		$html = $answerFormController->run();
		return $this->applicationView->getAnswerFormPage($html);
	}

	private function displayForms(\user\model\UserCredentials $user) {
		$listFormsController = new \form\controller\ListForms($user, $this->navigationView);
		$html = $listFormsController->run();
		return $this->applicationView->getListFormsPage($html);
	}

	private function viewResults(\user\model\UserCredentials $user) {
		$viewResultsController = new \form\controller\ViewResults($user, $this->navigationView);
		$html = $viewResultsController->run();
		return $this->applicationView->getFormResultPage($html);
	}

	private function showMySubmittedForm(\user\model\UserCredentials $user) {
		$manageSubmittedFormController = new \form\controller\ManageSubmittedForm($user, $this->navigationView);
		$html = $manageSubmittedFormController->run();
		return $this->applicationView->getAnswerFormPage($html);
	}

	private function delete(\user\model\UserCredentials $user) {
		$deleteController = new \form\controller\Delete($user, $this->navigationView);
		$deleteController->run();
	}
}