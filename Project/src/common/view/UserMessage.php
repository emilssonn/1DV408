<?php

namespace common\view;

/**
 * Messages displayed to user
 * Access by key by message
 * Access message by key
 */
class UserMessage {
	
	/**
	 * @var array of strings
	 * messageKey => message string
	 */
	private static $messages = array(
		//Authorization
		1001 => "Failed to verify user",
		1002 => "Missing Username", 
		1003 => "Missing Password",
		1004 => "Wrong Username and/or Password",
		1005 => "Faulty information in cookie",
		1006 => "Login by cookies successfull",
		1007 => "Login successfull and we will remember you for next time",
		1008 => "Login successfull",
		1009 => "You have been logged out",

		//Registration
		1101 => "Registration successfull",
		1102 => "The passwords do not match",
		//Must be compined with values, sprintf
		1103 => "Username to short. Minimum %d characters",
		1104 => "Passwords to short. Minimum %d characters",
		1105 => "Username contains invalid characters",
		1106 => "Username is already taken",

		//Form
		1201 => "Form Saved!",
		1202 => "Failed to save form! Please try again",
		1203 => "Failed to retrive form!",
		1204 => "Failed to retrive forms!",
		1205 => "Answers Saved!",
		1206 => "Failed to save answers! Please try again",
		1207 => "This form is not public.",
		1208 => "This form is no longer active.",
		1209 => "Failed to change form! Please try again",
		1210 => "Form changed!",
		1211 => "Form Deleted!",
		1212 => "Cant publish a form without atleast one question!",

		//Question
		1301 => "Question Saved!",
		1302 => "Failed to save question! Please try again",
		1303 => "Failed to retrive question!",
		1304 => "Failed to delete question! Please try again",
		1305 => "Question Deleted!");

	/**
	 * @param  int $key 
	 * @return string
	 * @throws \Exception If no message is found
	 */
	public static function getMessageByKey($key) {
		try {
			return self::$messages[$key];
		} catch (\Exception $e) {
			throw new \Exception('No message found for: $key');
		}
	}

	/**
	 * @param  string $message
	 * @return int
	 * @throws \Exception If no key is found
	 */
	public static function getKeyByMessage($message) {
		$key = array_search($message, self::$messages);
		if ($key === false)
			throw new \Exception('No key found for: $message');
		return $key;
	}
}