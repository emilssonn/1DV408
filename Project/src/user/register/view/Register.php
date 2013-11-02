<?php

namespace user\register\view; 

require_once("./src/common/Filter.php");
require_once("./src/user/register/model/RegisterObserver.php");

/**
 * @author Peter Emilsson
 * Responsible for registrating a user
 */
class Register implements \user\register\model\RegisterObserver {

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

	/**
	 * @var \application\view\Navigation
	 */
	private $navigationView;

	/**
	 * @param \application\view\Navigation $navigationView
	 */
	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
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
			<form action='?" . $this->navigationView->getRegister() . "' method='post' enctype='multipart/form-data' class='form-signin'>
				<fieldset>
					$htmlMessages
					<legend class='form-signin-heading'>Sign up</legend>

					<label for='UserNameID'>Username:</label>
					<input type='text' name='" . self::$USERNAME . "' id='UserNameID' value='$user' class='form-control' placeholder='Username' autofocus>
					
					<label for='PasswordID'>Password:</label>
					<input type='password' name='" . self::$PASSWORD . "' id='PasswordID' class='form-control' placeholder='Password'>

					<label for='PasswordID2' >Repeat password</label>
					<input type='password' name='" . self::$CONTROLLPASSWORD . "' id='PasswordID2' class='form-control' placeholder='Repeat password'>
					
					<input type='submit' value='Sign up' class='btn btn-lg btn-primary btn-block'>
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
	 * @throws \Exception If passwords do not match
	 */
	public function checkPasswords() {
		if ($this->getPassword() != $this->getPassword(false)) {
			$this->messages[] = \common\view\UserMessage::getMessageByKey(1102);
			throw new \Exception("Passwords do not match");
		}
		return true;
	}

	/**
	 * @return \user\model\UserCredentials
	 * @throws \Exception
	 */
	public function getUserCredentials() {
		$flag = true;
		try {
			$usernameModel = new \user\model\UserName(\common\Filter::trimString($this->getRawUserName()));
		} catch (\common\model\exception\StringLength $e) {
			$flag = false;
			$this->messages[] = sprintf(\common\view\UserMessage::getMessageByKey(1103),
										\user\model\Username::MINIMUM_USERNAME_LENGTH);
		} catch(\Exception $e) {
			$flag = false;
		}
		try {
			$passwordModel = \user\model\Password::fromCleartext($this->getPassword());
		} catch (\Exception $e) {
			$flag = false;
			$this->messages[] = sprintf(\common\view\UserMessage::getMessageByKey(1104),
									\user\model\Password::MINIMUM_PASSWORD_CHARACTERS);
		}
		
		if ($flag) {
			return \user\model\UserCredentials::createFromClientData($usernameModel, $passwordModel);
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
				return \common\Filter::sanitizeString($_POST[self::$PASSWORD]);
		} else {
			if (isset($_POST[self::$CONTROLLPASSWORD]))
				return \common\Filter::sanitizeString($_POST[self::$CONTROLLPASSWORD]);
		}
		return "";
	}

	/**
	 * @return string
	 */
	private function getUserName() {
		if (isset($_POST[self::$USERNAME])) 
			return \common\Filter::sanitizeString($_POST[self::$USERNAME]);
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

	/**
	 * Observer implementation
	 */
	
	public function registerFailed() {
		if (\common\Filter::hasTags($this->getRawUserName())) {
			$this->messages[] = \common\view\UserMessage::getMessageByKey(1105);
		}	
	}

	public function userExists() {
		$this->messages[] = \common\view\UserMessage::getMessageByKey(1106);
	}

	public function registerOK() {
		$this->messages[] = \common\view\UserMessage::getMessageByKey(1101);
	}
}