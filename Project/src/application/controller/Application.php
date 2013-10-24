<?php

namespace application\controller;

require_once("./src/user/authorization/view/Login.php");
require_once("./src/application/view/Application.php");
require_once("./src/user/authorization/controller/Login.php");
require_once("./src/application/view/Navigation.php");
require_once("./src/user/register/view/Register.php");
require_once("./src/user/register/controller/Register.php");
require_once("./src/home/view/Home.php");
require_once("./src/home/controller/Home.php");
require_once("./src/form/view/CreateForm.php");
require_once("./src/form/controller/CreateForm.php");

class Application {

	/**
	 * @var \view\Login
	 */
	private $loginView;

	/**
	 * @var \controller\Login
	 */
	private $loginController;

	/**
	 * @var \view\Application
	 */
	private $applicationView;

	private $navigationView;

	public function __construct(\common\view\PageView $pageView) {

		$this->navigationView = new \application\view\Navigation();
		$this->loginView = new \authorization\view\Login($this->navigationView);	
		$this->applicationView = new \application\view\Application($this->navigationView, $this->loginView, $pageView);	

		$this->loginController = new \authorization\controller\Login(
														$this->loginView, 
														$this->navigationView);

		
	}

	/**
	 * @return string HTML
	 */
	public function runApplication() {
		$this->loginController->doToggleLogin();
	
		if ($this->loginController->isLoggedIn()) {
			$loggedInUserCredentials = $this->loginController->getLoggedInUser();

			if ($this->navigationView->userHome()) {
				return $this->goHome($loggedInUserCredentials);

			} else if ($this->navigationView->addQuestion()) {
				return $this->addFormQuestion($loggedInUserCredentials);

			} else if ($this->navigationView->createForm() ||
						$this->navigationView->editForm()) {
				
				return $this->createForm($loggedInUserCredentials);
			}

			return $this->goHome($loggedInUserCredentials);
		} else if ($this->navigationView->wantsToRegister()) {
			$registerView = new \register\view\Register($this->navigationView);
			$registerController = new \register\controller\Register($registerView, $this->loginView);
			$registerController->doToggleRegister();
			if ($registerController->wasRegSuccessfull()) {
				return $this->applicationView->getLoggedOutPage();
			} else {
				return $this->applicationView->getRegisterPage($registerView);
			}
		} else {
			return $this->applicationView->getLoggedOutPage();
		}
	}

	private function goHome(\authorization\model\UserCredentials $user) {
		$homeView = new \home\view\Home($this->navigationView);
		$homeController = new \home\controller\Home($user, $homeView);
		$homeController->runHome();
		return $this->applicationView->getHomePage($homeView);
	}

	private function createForm(\authorization\model\UserCredentials $user) {
		
		$createFormController = new \form\controller\CreateForm($user, $this->navigationView);
		$html = $createFormController->runCreateForm();
		return $this->applicationView->getCreateFormPage($html);
	}

	private function addFormQuestion(\authorization\model\UserCredentials $user) {
		$createQuestionController = new \form\controller\CreateQuestion($this->navigationView, $user);
		$html = $createQuestionController->run();
		return $this->applicationView->getCreateQuestionPage($html);
	}

	private function displayForm(\authorization\model\UserCredentials $user) {

	}
}