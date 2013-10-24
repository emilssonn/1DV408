<?php

namespace application\view;

require_once("./src/common/view/Page.php");

class Application {

	private $navigationView;

	private $loginView;

	private $pageView;

	public function __construct(\application\view\Navigation $navigationView,
								\authorization\view\Login $loginView,
								\common\view\PageView $pageView) {
		$this->loginView = $loginView;
		$this->navigationView = $navigationView;
		$this->pageView = $pageView;
	}

	/**
	 * @return \common\view\Page
	 */
	public function getLoggedOutPage() {
		$html = $this->getHeader(false);
		$loginBox = $this->loginView->getLoginBox(); 
		$registerLink = $this->navigationView->getRegisterLink();
		$this->pageView->addStyleSheet("css/vendor/signin.css");

		$html .= "
				<div class='container'>
					<div class='well'>
						$loginBox
					</div>
					<div class='well'>
						$registerLink
					</div>
				</div>";
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
		$this->pageView->addStyleSheet("css/vendor/signin.css");

		$html .= "
				<div class='container'>
					<div class='well'>
						$registerForm
					</div>
					<div class='well'>
						<a href='.'>Back to sign in</a>
					</div>
				</div>";
		$html .= $this->getFooter();

		return new \common\view\Page("Sign up", $html);
	}

	public function getHomePage(\home\view\Home $homeView) {
		$html = $this->getHeader(true);
		$homePage = $homeView->getHTML();

		$html .= "
				<h1>Home</h1>
				$homePage";

		$html .= $this->getFooter();

		return new \common\view\Page("Home", $html);
	}

	public function getCreateFormPage($createFormHTML) {
		$html = $this->getHeader(true);

		$html .= "
				<div class='container'>
					$createFormHTML
				</div>";

		$html .= $this->getFooter();

		return new \common\view\Page("Create new form", $html);
	}

	public function getCreateQuestionPage($createQuestionHTML) {
		$html = $this->getHeader(true);

		$html .= "
				<div class='container'>
					$createQuestionHTML
				</div>";

		$html .= $this->getFooter();

		return new \common\view\Page("Add a question", $html);
	}

	/**
	 * @param boolean $isLoggedIn
	 * @return  String HTML
	 */
	private function getHeader($isLoggedIn) {
		$ret =  "";
		return $ret;
		
	}

	/**
	 * @return [type] [description]
	 */
	private function getFooter() {
		return "";
	}
}