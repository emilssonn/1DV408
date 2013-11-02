<?php

namespace application\view;

require_once("./src/common/view/Page.php");

/**
 * @author Peter Emilsson
 */
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
	 * @param application\view\Navigation $navigationView 
	 * @param authorization\view\Login    $loginView      
	 * @param common\view\PageView        $pageView      
	 */
	public function __construct(\application\view\Navigation $navigationView,
								\user\authorization\view\Login $loginView,
								\common\view\PageView $pageView) {
		$this->loginView = $loginView;
		$this->navigationView = $navigationView;
		$this->pageView = $pageView;
	}

	/**
	 * @return \common\view\Page
	 */
	public function getLoggedOutPage() {
		$loginBox = $this->loginView->getLoginBox(); 
		$registerLink = $this->navigationView->getRegisterLink();
		$this->pageView->addStyleSheet("css/vendor/signin.css");
		$this->addFormFiles();

		$html = "
				<div class='container'>
					<div class='well'>
						$loginBox
					</div>
					<div class='well'>
						$registerLink
					</div>
				</div>";

		return new \common\view\Page("Sign in", $html);
	}

	/**
	 * @param  \register\view\Register $registerView
	 * @return \common\view\Page
	 */
	public function getRegisterPage(\user\register\view\Register $registerView) {
		$registerForm = $registerView->getRegisterForm();
		$this->pageView->addStyleSheet("css/vendor/signin.css");
		$this->addFormFiles();

		$html = "
				<div class='container'>
					<div class='well'>
						$registerForm
					</div>
					<div class='well'>
						<a href='.'>Back to sign in</a>
					</div>
				</div>";

		return new \common\view\Page("Sign up", $html);
	}

	/**
	 * @param  string HTML $homeHTML
	 * @return \common\view\Page
	 */
	public function getHomePage($homeHTML) {
		$menu = $this->getMenu();
		$navBar = $this->getNavBar();

		$html = "
				$homeHTML";

		return new \common\view\Page("Home", $html, $menu, $navBar);
	}

	/**
	 * @param  String HTML $createFormHTML
	 * @return \common\view\Page
	 */
	public function getCreateFormPage($createFormHTML) {
		$this->pageView->addStyleSheet("css/vendor/jquery-ui-1.10.3.custom.min.css");
		$this->pageView->addStyleSheet("css/vendor/jquery-ui-timepicker-addon.css");
		$this->pageView->addJavaScript("javascript/vendor/jquery-ui-1.10.3.custom.min.js");
		$this->pageView->addJavaScript("javascript/vendor/jquery-ui-timepicker-addon.js");
		$this->pageView->addJavaScript("javascript/dateTimePicker.js");
		$this->addFormFiles();

		$menu = $this->getMenu();
		$navBar = $this->getNavBar();
		$html = "
			<div class='col-xs-12 col-sm-12 col-md-12'>
				$createFormHTML
			</div>";

		return new \common\view\Page("Create new form", $html, $menu, $navBar);
	}

	/**
	 * @param  String HTML $createQuestionHTML
	 * @return \common\view\Page
	 */
	public function getCreateQuestionPage($createQuestionHTML) {
		$this->addFormFiles();
		$menu = $this->getMenu();
		$navBar = $this->getNavBar();

		$html = "
			<div class='col-xs-12 col-sm-12 col-md-12'>
				$createQuestionHTML
			</div>";

		return new \common\view\Page("Add a question", $html, $menu, $navBar);
	}

	/**
	 * @param  string HTML $listFormsHTML 
	 * @return \common\view\Page
	 */
	public function getListFormsPage($listFormsHTML) {
		$menu = $this->getMenu();
		$navBar = $this->getNavBar();

		$html = "
			<div class='col-xs-12 col-sm-12 col-md-12'>
				$listFormsHTML
			</div>";

		return new \common\view\Page("All Forms", $html, $menu, $navBar);
	}

	/**
	 * @param  String HTML $answerFormHTML
	 * @return \common\view\Page
	 */
	public function getAnswerFormPage($answerFormHTML) {
		$this->addFormFiles();
		$menu = $this->getMenu();
		$navBar = $this->getNavBar();

		$html = "
			<div class='col-xs-12 col-sm-9 col-md-6'>
				$answerFormHTML
			</div>";

		return new \common\view\Page("Answer Form", $html, $menu, $navBar);
	}

	/**
	 * @param  string HTML $formResultHTML 
	 * @return \common\view\Page
	 */
	public function getFormResultPage($formResultHTML) {
		$this->pageView->addJavaScript("javascript/vendor/Chart.min.js");
		$this->pageView->addJavaScript("javascript/resultPageCharts.js");
		$menu = $this->getMenu();
		$navBar = $this->getNavBar();

		$html = "
			<div class='col-xs-12 col-sm-12 col-md-12'>
				$formResultHTML
			</div>";

		return new \common\view\Page("Form Results", $html, $menu, $navBar);
	}

	/**
	 * @return string HTML
	 */
	private function getMenu() {
		$this->pageView->addStyleSheet("css/vendor/simple-sidebar.css");
		$this->pageView->addJavaScript("javascript/vendor/simple-sidebar.js");
		$this->pageView->addJavaScript("javascript/vendor/alertFade.js");
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
			        <li><a href='$listForms'>Forms</a></li>
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

	/**
	 * Add form validation javascript
	 */
	private function addFormFiles() {
		$this->pageView->addJavaScript("//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.1.15/jquery.form-validator.min.js");
		$this->pageView->addJavaScript("javascript/validation.js");
	}

}