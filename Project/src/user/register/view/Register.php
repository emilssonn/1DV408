<?php

namespace register\view; 

require_once("./src/common/Filter.php");
require_once("./src/user/register/model/RegisterObserver.php");

class Register implements \register\model\RegisterObserver {

	/**
	 * @var string
	 */
	private static $USERNAME = "RegisterView::UserName";
	private static $PASSWORD = "RegisterView::Password";
	private static $CONTROLLPASSWORD = "RegisterView::CONTROLLPassword";

	/**
	 * @var array of strings
	 */
	private $messages = array();

	private $navigationView;

	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
	}


	/**
	 * @return string HTML
	 */
	public function getRegisterLink() {
		$register = $this->navigationView->getRegister();
		return "<a href='?$register'>Registrera ny användare</a>";
	}

	/**
	 * @return string HTML
	 */
	public function getRegisterForm() {
		$user = $this->getUserName();
		$htmlMessages = "";

		foreach ($this->messages as $key => $value) {
			$htmlMessages .= $value . "<br/>";
		}

		$html = "
			<form action='?" . $this->navigationView->getRegister() . "' method='post' enctype='multipart/form-data'>
				<fieldset>
					$htmlMessages
					<legend>Registrera ny användare - Skriv in användarnamn och lösenord</legend>
					<label for='UserNameID' >Användarnamn :</label>
					<input type='text' size='20' name='" . self::$USERNAME . "' id='UserNameID' value='$user' />
					<br/>
					<label for='PasswordID' >Lösenord  :</label>
					<input type='password' size='20' name='" . self::$PASSWORD . "' id='PasswordID' />
					<br/>
					<label for='PasswordID2' >Repetera Lösenord  :</label>
					<input type='password' size='20' name='" . self::$CONTROLLPASSWORD . "' id='PasswordID2' />
					<br/>

					<input type='submit' name=''  value='Registrera' />
				</fieldset>
			</form>";
			
		return $html;
	}

	/**
	 * @return boolean
	 */
	public function isRegistrating() {
		return isset($_GET[$this->navigationView->getRegister()]) && 
				strtolower($_SERVER['REQUEST_METHOD']) == "post";
	}

	/**
	 * @return boolean
	 * @throws Exception If passwords do not match
	 */
	public function checkPasswords() {
		if ($this->getPassword() != $this->getPassword(false)) {
			$this->messages[] = "Lösenorden matchar inte";
			throw new \Exception("Passwords do not match");
		}
		return true;
	}

	/**
	 * @return UserCredentials
	 */
	public function getUserCredentials() {
		$flag = true;
		try {
			$usernameModel = new \authorization\model\UserName(\Common\Filter::trimString($this->getRawUserName()));
		} catch(\Exception $e) {
			$flag = false;
			$this->messages[] = "Användarnamnet har för få tecken. Minst 3 tecken";
		}
		try {
			$passwordModel = \authorization\model\Password::fromCleartext($this->getPassword());
		} catch (\Exception $e) {
			$flag = false;
			$this->messages[] = "Lösenorden har för få tecken. Minst 6 tecken";
		}
		
		if ($flag) {
			return \authorization\model\UserCredentials::createFromClientData($usernameModel, $passwordModel);
		} else {
			throw new \Exception();
		}
	}

	/**
	 * @param  boolean $check
	 * @return string
	 */
	private function getPassword($check = true) {
		if ($check) {
			if (isset($_POST[self::$PASSWORD]))
				return \Common\Filter::sanitizeString($_POST[self::$PASSWORD]);
		} else {
			if (isset($_POST[self::$CONTROLLPASSWORD]))
				return \Common\Filter::sanitizeString($_POST[self::$CONTROLLPASSWORD]);
		}
		return "";
	}

	/**
	 * @return string
	 */
	private function getUserName() {
		if (isset($_POST[self::$USERNAME]))
			return \Common\Filter::sanitizeString($_POST[self::$USERNAME]);
		else
			return "";
	}

	/**
	 * @return string, unsanitized string
	 */
	private function getRawUserName() {
		if (isset($_POST[self::$USERNAME]))
			return $_POST[self::$USERNAME];
		else
			return "";
	}

	public function registerFailed() {
		if (\Common\Filter::hasTags($this->getRawUserName())) {
			$this->messages[] = "Användarnamnet innehåller ogiltiga tecken";
		}	
	}

	public function userExists() {
		$this->messages[] = "Användarnamnet är redan upptaget";
	}

	public function registerOK() {
		$this->messages[] = "Registrering av ny användare lyckades";
	}
}