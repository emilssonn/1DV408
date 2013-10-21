<?php

namespace application\view;

require_once("./src/common/view/Page.php");

class Application {

	private $navigationView;

	private $loginView;

	public function __construct(\application\view\Navigation $navigationView,
								\authorization\view\Login $loginView) {
		$this->loginView = $loginView;
		$this->navigationView = $navigationView;
	}

	/**
	 * @return \common\view\Page
	 */
	public function getLoggedOutPage() {
		$html = $this->getHeader(false);
		$loginBox = $this->loginView->getLoginBox(); 
		$registerLink = $this->navigationView->getRegisterLink();

		$html .= "
				<p>
					$registerLink
				</p>
				<h2>Ej Inloggad</h2>
				$loginBox
				 ";
		$html .= $this->getFooter();

		return new \common\view\Page("Sign in", $html);
	}

	/**
	 * @param \login\login\UserCredentials $user
	 * @return \common\view\Page
	 */
	public function getLoggedInPage(\authorization\model\UserCredentials $user) {
		$html = $this->getHeader(true);
		$logoutButton = $this->navigationView->getLogoutButton(); 
		$userName = $user->getUsername();

		$html .= "
				<h2>$userName är inloggad</h2>
				 	$logoutButton
				 ";
		$html .= $this->getFooter();

		return new \common\view\Page("Home", $html);
	}

	/**
	 * @return \common\view\Page
	 */
	public function getRegisterPage(\register\view\Register $registerView) {
		$html = $this->getHeader(false);
		$registerForm = $registerView->getRegisterForm();


		$html .= "
				<p>
				 	<a href='.'>Tillbaka</a>
				 </p>
				<h2>Ej inloggad, Registrera ny användare</h2>
				 	
				 	$registerForm
				 ";
		$html .= $this->getFooter();

		return new \common\view\Page("Sign up", $html);
	}

	/**
	 * @param boolean $isLoggedIn
	 * @return  String HTML
	 */
	private function getHeader($isLoggedIn) {
		$ret =  "<h1>Laborationskod xx222aa</h1>";
		return $ret;
		
	}

	/**
	 * @return [type] [description]
	 */
	private function getFooter() {
		return "<p>footer<p>";
	}
}