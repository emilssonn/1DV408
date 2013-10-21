<?php

namespace common\view;

class ErrorMessage {
	private static $errorMessages = array(
		//Authorization
		1001 => "Användarnamn saknas", 
		1002 => "Lösenord saknas",
		1003 => "Felaktigt användarnamn och/eller lösenord",
		1004 => "Felaktig information i cookie",
		1005 => "Inloggning lyckades via cookies",
		1006 => "Inloggning lyckades och vi kommer ihåg dig nästa gång",
		1007 => "Inloggning lyckades",
		1008 => "Du har nu loggat ut",

		//Registration
		1101 => "Registrering av ny användare lyckades",
		1102 => "Lösenorden matchar inte",
		1103 => "Användarnamnet har för få tecken. Minst 3 tecken",
		1104 => "Lösenorden har för få tecken. Minst 6 tecken",
		1105 => "Användarnamnet innehåller ogiltiga tecken",
		1106 => "Användarnamnet är redan upptaget");


	public static function getByInt($int) {
		try {
			return self::$errorMessages[$int];
		} catch (\Exception $e) {
			return "";
		}
	}
}