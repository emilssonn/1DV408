<?php

namespace user\authorization\view;

require_once("./src/user/authorization/model/User.php");
require_once("./src/user/authorization/model/LoginObserver.php");
require_once("./src/common/filter.php");
require_once("./src/common/view/UserMessage.php");

/**
 * @author Daniel Toll - https://github.com/dntoll
 * Changes by Peter Emilsson
 */
class Login implements \user\authorization\model\LoginObserver {

	/**
	 * Name in HTML form and location in $_POST
	 * @var string
	 */
	private static $usernamePOST = "View::Login::Username";

	/**
	 * Name in HTML form and location in $_POST
	 * @var string
	 */
	private static $passwordPOST = "View::Login::Password";

	/**
	 * Name in HTML form and location in $_POST
	 * @var string
	 */
	private static $cookieLoginPOST = "View::Login::CookieLogin";

	private static $CHECKED = "LoginView::Checked";

	/**
	 * @var \model\User
	 */
	private $userModel;

	/**
	 * @var string
	 */
	private $message = null;

	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var \application\view\Navigation
	 */
	private $navigationView;

	/**
	 * @param \view\Application $applicationView
	 */
	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
	}

	/**
	 * @return String HTML
	 */
	public function getLoginBox() { 
		$user = $this->getUsername();
		$checked = $this->userWantsToBeRemembered() ? "checked=checked" : "";

		$html = "
			<form action='?" . $this->navigationView->getLogin() . "' method='post' enctype='multipart/form-data' class='form-signin'>
				<fieldset>
					$this->message
					<legend class='form-signin-heading'>Sign in</legend>
					
					<label for='UserNameID'>Username:</label>
					<input type='text' size='20' name='" . self::$usernamePOST . "' id='UserNameID' value='$user' class='form-control' placeholder='Username' autofocus>
					
					<label for='PasswordID'>Password:</label>
					<input type='password' size='20' name='" . self::$passwordPOST . "' id='PasswordID' class='form-control' placeholder='Password'>
						
					<label for='AutologinID' class='checkbox'>
						<input type='checkbox' name='" . self::$CHECKED . "' id='AutologinID' $checked>
						Remember me
					</label>

					<input type='submit' value='Sign in' class='btn btn-lg btn-primary btn-block'>
				</fieldset>
			</form>";
			
		return $html;
	}

	/**
	 * @return boolean
	 */
	public function isLoggingOut() {
		return isset($_GET[$this->navigationView->getLogOut()]);
	}
	
	/**
	 * @return boolean
	 */
	public function isLoggingIn() {
		if (isset($_GET[$this->navigationView->getLogin()]) && 
			strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
			return true;
		} else if ($this->hasCookies()) {
			return true;
		} else {
			return false;
		}
	}

	public function doLogout() {
		$this->removeCookies();
		$message = \common\view\UserMessage::getMessageByKey(1009);
		$this->message .= "<p>$message</p>";
	}
	
	/**
	 * @return \user\model\UserCredentials
	 */
	public function getUserCredentials() {
		if ($this->hasCookies()) {
			return \user\model\UserCredentials::createWithTempPassword(
						new \user\model\Username($this->getUsername()), 
						$this->getTemporaryPassword());
		} else {
			return \user\model\UserCredentials::createFromClientData(
						new \user\model\Username($this->getUsername()), 
						\user\model\Password::fromCleartext($this->getPassword()));
		}
	}

	/**
	 * @return String
	 */
	private function getUsername() {
		if (isset($_POST[self::$usernamePOST]))
			return \common\Filter::sanitizeString($_POST[self::$usernamePOST]);
		else if (isset($_COOKIE[self::$usernamePOST]))
			return \common\Filter::sanitizeString($_COOKIE[self::$usernamePOST]);
		else
			return "";
	}
	
	/** 
	 * @return String
	 */
	private function getPassword() {
		if (isset($_POST[self::$passwordPOST]))
			return \common\Filter::sanitizeString($_POST[self::$passwordPOST]);
		else
			return "";
	}
	
	/**
	 * If user checks the remember me checkbox
	 * @return boolean 
	 */
	public function userWantsToBeRemembered() {
		return isset($_POST[self::$CHECKED]);
	}

	/**
	 * Get cookie password
	 * @return TemporaryPasswordClient
	 */
	private function getTemporaryPassword() {
		if (isset($_COOKIE[self::$passwordPOST])) {
			$fromCookieString = \common\Filter::sanitizeString($_COOKIE[self::$passwordPOST]);
			return \user\model\TemporaryPasswordClient::fromString($fromCookieString);
		} else {
			return \user\model\TemporaryPasswordClient::emptyPassword();
		}
	}

	/**
	 * Did user supply cookie password?
	 * 
	 * @return boolean 
	 */
	private function hasCookies() {
		return isset($_COOKIE[self::$passwordPOST]) && isset($_COOKIE[self::$usernamePOST]);
	}

	/**
	 * Removes cookies from client 
	 */
	private function removeCookies() {

		unset($_COOKIE[self::$usernamePOST]);
		unset($_COOKIE[self::$passwordPOST]);
			
		$expireNow = time()-60;
		setcookie(self::$usernamePOST, "", $expireNow);
		setcookie(self::$passwordPOST, "", $expireNow);
	}

	/**
	 * From \model\LoginObserver
	 */
	public function loginFailed() {
		if ($this->hasCookies()) {
			$message = \common\view\UserMessage::getMessageByKey(1005);
			$this->message .= "<p>$message</p>";
			$this->removeCookies();
		} else { 
			if ($this->getUsername() == "") {
				$message = \common\view\UserMessage::getMessageByKey(1002);
				$this->message .= "<p>$message</p>";
			} else if ($this->getPassword() == "") {
				$message = \common\view\UserMessage::getMessageByKey(1003);
				$this->message .= "<p>$message</p>";
			} else {
				$message = \common\view\UserMessage::getMessageByKey(1004);
				$this->message .= "<p>$message</p>";
			}
		}
	}
	
	/**
	 * From \model\LoginObserver
	 */
	public function loginOK(\user\model\TemporaryPasswordServer $tempCookie,
							$rememberMe = false) {
		if ($rememberMe) {
			$message = \common\view\UserMessage::getMessageByKey(1007);
			$this->message .= "<p>$message</p>";
			$expire = $tempCookie->getExpireDate();
			setcookie(self::$usernamePOST, $this->getUsername(), $expire, "", "", false , true);
			setcookie(self::$passwordPOST, $tempCookie->getTemporaryPassword(), $expire, "", "", false , true);
		} else if ($this->hasCookies()) {
			$message = \common\view\UserMessage::getMessageByKey(1006);
			$this->message .= "<p>$message</p>";
		} else {
			$message = \common\view\UserMessage::getMessageByKey(1008);
			$this->message .= "<p>$message</p>";
		}
	}

	/**
	 * @param  \login\model\UserCredentials $userCred
	 */
	public function registerOk(\user\model\UserCredentials $userCred) {
		$message = \common\view\UserMessage::getMessageByKey(1101);
		$this->message .= "<p>$message</p>";
		$this->regUsername = $userCred->getUserName();
	}
}