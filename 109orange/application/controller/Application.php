<?php

namespace application\controller;

require_once("application/view/View.php");
require_once("login/controller/LoginController.php");
require_once("register/controller/Register.php");
require_once("register/view/Register.php");


/**
 * Main application controller
 */
class Application {
	/**
	 * \view\view
	 * @var [type]
	 */
	private $view;

	/**
	 * @var \login\controller\LoginController
	 */
	private $loginController;

	/**
	 * @var \register\controller\Register
	 */
	private $registerController;
	
	public function __construct() {
		$loginView = new \login\view\LoginView();
		$registerView = new \register\view\Register();
		
		$this->loginController = new \login\controller\LoginController($loginView);
		$this->registerController = new \register\controller\Register($registerView, $loginView);

		$this->view = new \application\view\View($loginView, $registerView);
	}
	
	/**
	 * @return \common\view\Page
	 */
	public function doFrontPage() {
		$this->loginController->doToggleLogin();
		$this->registerController->doToggleRegister();
	
		if ($this->loginController->isLoggedIn()) {
			$loggedInUserCredentials = $this->loginController->getLoggedInUser();
			return $this->view->getLoggedInPage($loggedInUserCredentials);	
		} else if (	$this->registerController->wantsToRegister() && 
					$this->registerController->wasRegSuccessfull()) {

			return $this->view->getLoggedOutPage();
		} else if ($this->registerController->wantsToRegister()) {
			return $this->view->getRegisterPage();
		} else {
			return $this->view->getLoggedOutPage();
		}
	}
}
