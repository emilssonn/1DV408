<?php

namespace view;

require_once("./src/view/Time.php");
require_once("./src/view/HTMLPage.php");

class Application {

	/**
	 * @var \view\Time
	 */
	private $timeView;

	/**
	 * @var \view\HTMLPage
	 */
	private $htmlPageView;

	/**
	 * Name in URL for login
	 * @var string
	 */
	private static $loginGET = "login";

	/**
	 * Name in URL for logout
	 * @var string
	 */
	private static $logoutGET = "logout";

	/**
	 * @var string
	 */
	private $pageTitle = "Laboration: Ej inloggad";

	public function __construct() {
		$this->timeView = new \View\Time();
		$this->htmlPageView = new \view\HTMLPage();
	}

	/**
	 * @param string $title
	 */
	public function setPageTitle($title) {
		$this->pageTitle = $title;
	}

	/**
	 * Set the title
	 */
	public function loggedInTitle() {
		$this->setPageTitle("Laboration: Inloggad");
	}

	/**
	 * @return string
	 */
	public function getLoginLink() {
		return self::$loginGET;
	}

	/**
	 * @return string
	 */
	public function getLogOutLink() {
		return self::$logoutGET;
	}

	/**
	 * true if user wants to login
	 * @return bool
	 */
	public function formLogin() {
		return isset($_GET[self::$loginGET]) && $_SERVER['REQUEST_METHOD'] === 'POST';
	}

	/**
	 * true if user wants to logout
	 * @return bool
	 */
	public function userLogout() {
		return isset($_GET[self::$logoutGET]);
	}

	/**
	 * @return string
	 */
	public function getIPAdress() {
		return $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * @return string
	 */
	public function getUserAgent() {
		return $_SERVER['HTTP_USER_AGENT'];
	}

	/**
	 * @param  string HTML $body
	 * @return string HTML
	 */
	public function getPageHTML($body) {
		$timeString = $this->timeView->getFullTimeString();
		$body .= 	"<hr/>
					<p>$timeString</p>";
		return $this->htmlPageView->getHTML($this->pageTitle, $body);
	}
}