<?php

namespace application\view;

require_once("common/view/Page.php");
require_once("SwedishDateTimeView.php");



class View {
	/**
	 * @var \Login\view\LoginView
	 */
	private $loginView;

	/**
	 * @var  SwedishDateTimeView $timeView;
	 */
	private $timeView;

	/**
	 * @var \register\view\Register
	 */
	private $registerView;
	
	/**
	 * @param LoginviewLoginView $loginView 
	 */
	public function __construct(\login\view\LoginView $loginView, \register\view\Register $registerView) {
		$this->loginView = $loginView;
		$this->registerView = $registerView;
		$this->timeView = new SwedishDateTimeView();
	}
	
	/**
	 * @return \common\view\Page
	 */
	public function getLoggedOutPage() {
		$html = $this->getHeader(false);
		$registerLink = $this->registerView->getRegisterLink();
		$loginBox = $this->loginView->getLoginBox(); 

		$html .= "
				<p>
					$registerLink
				</p>
				<h2>Ej Inloggad</h2>
				$loginBox
				 ";
		$html .= $this->getFooter();

		return new \common\view\Page("Laboration. Inte inloggad", $html);
	}
	
	/**
	 * @param \login\login\UserCredentials $user
	 * @return \common\view\Page
	 */
	public function getLoggedInPage(\login\model\UserCredentials $user) {
		$html = $this->getHeader(true);
		$logoutButton = $this->loginView->getLogoutButton(); 
		$userName = $user->getUserName();

		$html .= "
				<h2>$userName är inloggad</h2>
				 	$logoutButton
				 ";
		$html .= $this->getFooter();

		return new \common\view\Page("Laboration. Inloggad", $html);
	}
	
	/**
	 * @return \common\view\Page
	 */
	public function getRegisterPage() {
		$html = $this->getHeader(false);
		$registerForm = $this->registerView->getRegisterForm();


		$html .= "
				<p>
				 	<a href='.'>Tillbaka</a>
				 </p>
				<h2>Ej inloggad, Registrera ny användare</h2>
				 	
				 	$registerForm
				 ";
		$html .= $this->getFooter();

		return new \common\view\Page("Laboration. Registrering av ny användare", $html);
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
		$timeString = $this->timeView->getTimeString(time());
		return "<p>$timeString<p>";
	}
}
