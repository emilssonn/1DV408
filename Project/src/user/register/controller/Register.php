<?php

namespace user\register\controller; 

require_once("./src/user/register/model/Register.php");
require_once("./src/user/register/view/Register.php");
require_once("./src/common/controller/IController.php");

/**
 * @author Peter Emilsson
 * Responsible for registrating a user
 */
class Register implements \common\controller\IController {

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

	public function __construct(\user\register\view\Register $registerView, 
								\user\authorization\view\Login $loginView) {
		$this->registerView = $registerView;
		$this->loginView = $loginView;
		$this->registerModel = new \user\register\model\Register();
	}

	/**
	 * @return boolean
	 */
	public function wasRegSuccessfull() {
		return $this->regSuccessfull;
	}

	public function run() {
		if ($this->registerView->isRegistrating() ) {
			try {
				if ($this->registerView->checkPasswords()) {
					$credentials = $this->registerView->getUserCredentials();
					$this->registerModel->doRegister($credentials, $this->registerView);
					$this->regSuccessfull = true;
					$this->loginView->registerOk($credentials);
				}
				
			} catch (\Exception $e) {
				$this->registerView->registerFailed();
			}
		}	
	}
}