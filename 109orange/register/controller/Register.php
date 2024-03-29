<?php

namespace register\controller; 

require_once("./register/model/Register.php");
require_once("./register/view/Register.php");

class Register {

	/**
	 * @var \register\view\Register
	 */
	private $registerView;

	/**
	 * @var \register\model\Register
	 */
	private $registerModel;

	/**
	 * @var \login\view\LoginView
	 */
	private $loginView;

	/**
	 * @var boolean
	 */
	private $regSuccessfull = false;

	public function __construct(\register\view\Register $registerView, \login\view\LoginView $loginView) {
		$this->registerView = $registerView;
		$this->loginView = $loginView;
		$this->registerModel = new \register\model\Register();;
	}

	/**
	 * @return boolean
	 */
	public function wantsToRegister() {
		return $this->registerView->wantsToRegister();
	}

	/**
	 * @return boolean
	 */
	public function wasRegSuccessfull() {
		return $this->regSuccessfull;
	}

	/**
	 * Handle input
	 * Make sure to log statechanges
	 *
	 * note this has no output, output goes through views that are called seperately
	 */
	public function doToggleRegister() {
		if ($this->registerView->isRegistrating() ) {
			try {
				if ($this->registerView->checkPasswords()) {
					$credentials = $this->registerView->getUserCredentials();
					$this->registerModel->doRegister($credentials, $this->registerView);
					$this->regSuccessfull = true;
					$this->loginView->registerOk($credentials);
				}
				
				\Debug::log("Registration succeded");
			} catch (\Exception $e) {
				\Debug::log("Registration failed", false, $e->getMessage());
				$this->registerView->registerFailed();
			}
		}
		
	}
}