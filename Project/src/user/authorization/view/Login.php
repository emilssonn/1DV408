<?php

namespace authorization\view;

require_once("./src/user/authorization/model/User.php");
require_once("./src/user/authorization/model/LoginObserver.php");
require_once("./src/common/filter.php");
require_once("./src/common/view/ErrorMessage.php");

class Login implements \authorization\model\LoginObserver {

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
			<form action='?" . $this->navigationView->getLogin() . "' method='post' enctype='multipart/form-data'>
				<fieldset>
					$this->message
					<legend>Login - Skriv in användarnamn och lösenord</legend>
					<label for='UserNameID' >Användarnamn :</label>
					<input type='text' size='20' name='" . self::$usernamePOST . "' id='UserNameID' value='$user' />
					<label for='PasswordID' >Lösenord  :</label>
					<input type='password' size='20' name='" . self::$passwordPOST . "' id='PasswordID' value='' />
					<label for='AutologinID' >Håll mig inloggad  :</label>
					<input type='checkbox' name='" . self::$CHECKED . "' id='AutologinID' $checked/>
					<input type='submit' name=''  value='Logga in' />
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
		
		$this->message  = "<p>Du har nu loggat ut</p>";
	}
	
	/**
	 * @return UserCredentials
	 */
	public function getUserCredentials() {
		if ($this->hasCookies()) {
			return \authorization\model\UserCredentials::createWithTempPassword(
						new \authorization\model\Username($this->getUsername()), 
						$this->getTemporaryPassword());
		} else {
			return \authorization\model\UserCredentials::createFromClientData(
						new \authorization\model\Username($this->getUsername()), 
						\authorization\model\Password::fromCleartext($this->getPassword()));
		}
	}

	/**
	 * note: private!
	 * @return String
	 */
	private function getUsername() {
		if (isset($_POST[self::$usernamePOST]))
			return \Common\Filter::sanitizeString($_POST[self::$usernamePOST]);
		else if (isset($_COOKIE[self::$usernamePOST]))
			return \Common\Filter::sanitizeString($_COOKIE[self::$usernamePOST]);
		else
			return "";
	}
	
	/**
	 * note: private!
	 * 
	 * @return String
	 */
	private function getPassword() {
		if (isset($_POST[self::$passwordPOST]))
			return \Common\Filter::sanitizeString($_POST[self::$passwordPOST]);
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
			$fromCookieString = \Common\Filter::sanitizeString($_COOKIE[self::$passwordPOST]);
			return \authorization\model\TemporaryPasswordClient::fromString($fromCookieString);
		} else {
			return \authorization\model\TemporaryPasswordClient::emptyPassword();
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
			$this->message = "<p>Felaktig information i cookie</p>";
			$this->removeCookies();
		} else { 
			if ($this->getUsername() == "") {
				$emessage = \common\view\ErrorMessage::getByInt(1001);
				$this->message .= "<p>$emessage</p>";
			} else if ($this->getPassword() == "") {
				$this->message .= "<p>Lösenord saknas</p>";
			} else {
				$this->message = "<p>Felaktigt användarnamn och/eller lösenord</p>";
			}
		}
	}
	
	/**
	 * From \model\LoginObserver
	 */
	public function loginOK(\authorization\model\TemporaryPasswordServer $tempCookie,
							$rememberMe = false) {
		if ($rememberMe) {
			$this->message  = "<p>Inloggning lyckades och vi kommer ihåg dig nästa gång</p>";
			$expire = $tempCookie->getExpireDate();
			setcookie(self::$usernamePOST, $this->getUsername(), $expire);
			setcookie(self::$passwordPOST, $tempCookie->getTemporaryPassword(), $expire);
		} else if ($this->hasCookies()) {
			$this->message  = "<p>Inloggning lyckades via cookies</p>";
		} else {
			$this->message  = "<p>Inloggning lyckades</p>";
		}
	}

	/**
	 * @param  \login\model\UserCredentials $userCred
	 */
	public function registerOk(\authorization\model\UserCredentials $userCred) {
		$this->message = "<p>Registrering av ny användare lyckades</p>";
		$this->regUsername = $userCred->getUserName();
	}
}