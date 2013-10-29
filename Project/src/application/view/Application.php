<?php

namespace application\view;

require_once("./src/common/view/Page.php");

class Application {

	/**
	 * @var \application\view\Navigation
	 */
	private $navigationView;

	/**
	 * @var user\authorization\view\Login
	 */
	private $loginView;

	/**
	 * @var common\view\PageView
	 */
	private $pageView;

	/**
	 * @param application\view\Navigation $navigationView [description]
	 * @param authorization\view\Login    $loginView      [description]
	 * @param common\view\PageView        $pageView       [description]
	 */
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
	 * @param  register\view\Register $registerView [description]
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

	/**
	 * @param  home\view\Home $homeView [description]
	 * @return \common\view\Page
	 */
	public function getHomePage(\home\view\Home $homeView) {
		$this->pageView->addStyleSheet("css/vendor/simple-sidebar.css");
		$this->pageView->addJavaScript("javascript/vendor/simple-sidebar.js");
		$menu = $this->getMenu();
		$navBar = $this->getNavBar();
		$html = $this->getHeader(true);
		$homePage = $homeView->getHTML();

		$html .= "
				<h1>Home</h1>
				$homePage";

		$html .= $this->getFooter();

		return new \common\view\Page("Home", $html, $menu, $navBar);
	}

	/**
	 * [getCreateFormPage description]
	 * @param  String HTML $createFormHTML
	 * @return \common\view\Page
	 */
	public function getCreateFormPage($createFormHTML) {
		$this->pageView->addStyleSheet("css/vendor/simple-sidebar.css");
		$this->pageView->addJavaScript("javascript/vendor/simple-sidebar.js");
		$menu = $this->getMenu();
		$navBar = $this->getNavBar();
		$html = $this->getHeader(true);

		$html .= "
			<div class='col-xs-12 col-sm-9 col-md-6'>
				$createFormHTML
			</div>";

		$html .= $this->getFooter();

		return new \common\view\Page("Create new form", $html, $menu, $navBar);
	}

	/**
	 * [getCreateQuestionPage description]
	 * @param  String HTML $createQuestionHTML
	 * @return \common\view\Page
	 */
	public function getCreateQuestionPage($createQuestionHTML) {
		$this->pageView->addStyleSheet("css/vendor/simple-sidebar.css");
		$this->pageView->addJavaScript("javascript/vendor/simple-sidebar.js");
		$menu = $this->getMenu();
		$navBar = $this->getNavBar();
		$html = $this->getHeader(true);

		$html .= "
			<div class='col-xs-12 col-sm-9 col-md-6'>
				$createQuestionHTML
			</div>";

		$html .= $this->getFooter();

		return new \common\view\Page("Add a question", $html, $menu, $navBar);
	}

	/**
	 * [getListFormsPage description]
	 * @param  string HTML $listFormsHTML 
	 * @return \common\view\Page
	 */
	public function getListFormsPage($listFormsHTML) {
		$this->pageView->addStyleSheet("css/vendor/simple-sidebar.css");
		$this->pageView->addJavaScript("javascript/vendor/simple-sidebar.js");
		$menu = $this->getMenu();
		$navBar = $this->getNavBar();
		$html = $this->getHeader(true);

		$html .= "
			<div class='col-xs-12 col-sm-12 col-md-12'>
				$listFormsHTML
			</div>";

		$html .= $this->getFooter();

		return new \common\view\Page("All Forms", $html, $menu, $navBar);
	}

	/**
	 * [getAnswerFormPage description]
	 * @param  String HTML $answerFormHTML
	 * @return \common\view\Page
	 */
	public function getAnswerFormPage($answerFormHTML) {
		$this->pageView->addStyleSheet("css/vendor/simple-sidebar.css");
		$this->pageView->addJavaScript("javascript/vendor/simple-sidebar.js");
		$menu = $this->getMenu();
		$navBar = $this->getNavBar();
		$html = $this->getHeader(true);

		$html .= "
			<div class='col-xs-12 col-sm-9 col-md-6'>
				$answerFormHTML
			</div>";

		$html .= $this->getFooter();

		return new \common\view\Page("Answer Form", $html, $menu, $navBar);
	}

	/**
	 * [getFormResultPage description]
	 * @param  string HTML $formResultHTML 
	 * @return \common\view\Page
	 */
	public function getFormResultPage($formResultHTML) {
		$this->pageView->addStyleSheet("css/vendor/simple-sidebar.css");
		$this->pageView->addJavaScript("javascript/vendor/simple-sidebar.js");
		$this->pageView->addJavaScript("javascript/vendor/Chart.min.js");
		$this->pageView->addJavaScript("javascript/resultPageCharts.js");

		$menu = $this->getMenu();
		$navBar = $this->getNavBar();
		$html = $this->getHeader(true);

		$html .= "
			<div class='col-xs-12 col-sm-12 col-md-12'>
				$formResultHTML
			</div>";

		$html .= $this->getFooter();

		return new \common\view\Page("Form Results", $html, $menu, $navBar);
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

	/**
	 * @return string HTML
	 */
	private function getMenu() {
		$home = $this->navigationView->getGoToHomeLink();
		$create = $this->navigationView->getGoToCreateFormLink();
		$listForms = $this->navigationView->getListFormsLink();
		$listMyForms = $this->navigationView->getListMyFormsLink();
		$listMySubmittedForms = $this->navigationView->getListMySubmittedFormsLink();
		return "
			<div id='sidebar-wrapper'>
        		<ul class='sidebar-nav'>
          			<li class='sidebar-brand'><a href='#'>Beta</a></li>
          			<li><a href='$home'>Home</a></li>
			        <li><a href='$create'>Create New Form</a></li>
			        <li><a href='$listMyForms'>Manage My Forms</a></li>
			        <li><a href='$listMySubmittedForms'>My Submitted Forms</a></li>
			        <li><a href='$listForms'>Find Forms</a></li>
        		</ul>
      		</div>";
	}

	/**
	 * @return string HTML
	 */
	private function getNavBar() {
		$home = $this->navigationView->getGoToHomeLink();
		$logOut = $this->navigationView->getLogoutLink();
		return "
			<div class='navbar navbar-fixed-top navbar-default' role='navigation'>

				<div class='navbar-header'>

					<a class='navbar-brand' href='#'>Simple forms</a>
					<button type='button' class='navbar-toggle' id='menu-toggle'>
						<span class='icon-bar'></span>
						<span class='icon-bar'></span>
						<span class='icon-bar'></span>
					</button>

						          
				</div>

				<div class='collapse navbar-collapse'>

					<ul class='nav navbar-nav navbar-right'>
						<li><a href='$logOut'>Sign Out</a></li>
					</ul>
				</div><!-- /.nav-collapse -->
			</div><!-- /.navbar -->";
	}

}