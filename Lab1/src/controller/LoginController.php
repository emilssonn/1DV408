<?php

namespace Controller;

require_once("/../view/LoginView.php");
require_once("/../model/UserModel.php");

/**
 * 
 */
class LoginController {

	/**
	 * [$loginView description]
	 * @var [type]
	 */
	private $loginView;

	/**
	 * [$user description]
	 * @var [type]
	 */
	private $user;

	/**
	 * [$userLogin description]
	 * @var [type]
	 */
	private $userLogin;

	/**
	 * [__construct description]
	 * @param modelUser      $user      [description]
	 * @param ModelUserLogin $userLogin [description]
	 */
	public function __construct(\model\User $user, \Model\UserLogin $userLogin) {
		$this->loginView = new \view\LoginView();
		$this->user = $user;
		$this->userLogin = $userLogin;
	}

	/**
	 * [checkUser description]
	 * @return [type] [description]
	 */
	public function checkUser() {
		if($this->user->isUserLoggedIn() && !$this->loginView->userWantsToLogout() ) {
			
			return $this->loginView->getLoggedInHTML($this->user);
		}
		else if ($this->loginView->userWantsToLogin() && !$this->user->isUserLoggedIn()) {
			
			try {
				$userInfo = $this->loginView->getLoginInfo();
				if ($this->user->login($userInfo["username"], $userInfo["password"])) {
					
					$this->userLogin->saveUser($this->user);
					return $this->loginView->getLoggedInHTML($this->user, "Inloggningen lyckades");
				} else {
					
					return $this->loginView->getLoginForm($this->user, "Felaktigt användarnamn och/eller lösenord");
				}

			} catch(\Exception $e) {
				
				return $this->loginView->getLoginForm($this->user, $e->getMessage());
			}
		} else if ($this->user->isUserLoggedIn() && $this->loginView->userWantsToLogout() ) {
			
			$this->userLogin->logout();
			$this->user->logout();
			return $this->loginView->getLoginForm($this->user, "Utloggningen lyckades");
		} else {
			
			return $this->loginView->getLoginForm($this->user);
		}
	}
}