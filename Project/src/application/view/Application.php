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
		$this->pageView->addStyleSheet("css/vendor/simple-sidebar.css");
		$this->pageView->addJavaScript("javascript/vendor/simple-sidebar.js");
		$menu = $this->getMenu();
		$html = $this->getHeader(true);
		$homePage = $homeView->getHTML();

		$html .= "
				<h1>Home</h1>
				$homePage";

		$html .= $this->getFooter();

		return new \common\view\Page("Home", $html, $menu);
	}

	public function getCreateFormPage($createFormHTML) {
		$this->pageView->addStyleSheet("css/vendor/simple-sidebar.css");
		$this->pageView->addJavaScript("javascript/vendor/simple-sidebar.js");
		$menu = $this->getMenu();
		$html = $this->getHeader(true);

		$html .= "
			<div class='col-xs-12 col-sm-9 col-md-6'>
				$createFormHTML
			</div>";

		$html .= $this->getFooter();

		return new \common\view\Page("Create new form", $html, $menu);
	}

	public function getCreateQuestionPage($createQuestionHTML) {
		$this->pageView->addStyleSheet("css/vendor/simple-sidebar.css");
		$this->pageView->addJavaScript("javascript/vendor/simple-sidebar.js");
		$menu = $this->getMenu();
		$html = $this->getHeader(true);

		$html .= "
			<div class='col-xs-12 col-sm-9 col-md-6'>
				$createQuestionHTML
			</div>";

		$html .= $this->getFooter();

		return new \common\view\Page("Add a question", $html, $menu);
	}

	public function getListFormsPage($listFormsHTML) {
		$this->pageView->addStyleSheet("css/vendor/simple-sidebar.css");
		$this->pageView->addJavaScript("javascript/vendor/simple-sidebar.js");
		$menu = $this->getMenu();
		$html = $this->getHeader(true);

		$html .= "
			<div class='col-xs-12 col-sm-12 col-md-12'>
				$listFormsHTML
			</div>";

		$html .= $this->getFooter();

		return new \common\view\Page("All Forms", $html, $menu);
	}

	public function getAnswerFormPage($answerFormHTML) {
		$this->pageView->addStyleSheet("css/vendor/simple-sidebar.css");
		$this->pageView->addJavaScript("javascript/vendor/simple-sidebar.js");
		$menu = $this->getMenu();
		$html = $this->getHeader(true);

		$html .= "
			<div class='col-xs-12 col-sm-9 col-md-6'>
				$answerFormHTML
			</div>";

		$html .= $this->getFooter();

		return new \common\view\Page("Answer Form", $html, $menu);
	}

	public function getFormResultPage($formResultHTML) {
		$this->pageView->addStyleSheet("css/vendor/simple-sidebar.css");
		$this->pageView->addJavaScript("javascript/vendor/simple-sidebar.js");
		$menu = $this->getMenu();
		$html = $this->getHeader(true);

		$html .= "
			<div class='col-xs-12 col-sm-9 col-md-6'>
				$formResultHTML
			</div>";

		$html .= $this->getFooter();

		return new \common\view\Page("Form Results", $html, $menu);
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

	private function getMenu() {
		$home = $this->navigationView->getGoToHomeLink();
		$create = $this->navigationView->getGoToCreateFormLink();
		$listForms = $this->navigationView->getListFormsLink();
		$listMyForms = $this->navigationView->getListMyFormsLink();
		return "
			<div id='sidebar-wrapper'>
        		<ul class='sidebar-nav'>
          			<li class='sidebar-brand'><a href='#'>Start Bootstrap</a></li>
          			<li><a href='$home'>Home</a></li>
			        <li><a href='$create'>Create New Form</a></li>
			        <li><a href='$listMyForms'>Manage My Forms</a></li>
			        <li><a href='$listForms'>Find Forms</a></li>
			        <li><a href='#'>About</a></li>
			        <li><a href='#'>Services</a></li>
			        <li><a href='#'>Contact</a></li>
        		</ul>
      		</div>";
	}

}