<?php

namespace View;

require_once("/../model/Time.php");

class HTMLPage {

	/**
	 * HTML/CSS from http://getbootstrap.com/examples/signin/
	 */
	public function getHTML($title, $body) {
		$time = new \Model\Time();

		$timeString = $time->getFullTimeString();

		return "
			<!DOCTYPE html>
			<html lang='sv'>
				<head>
					<meta charset='utf-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1.0'>

					<title>$title</title>

					<link href='css/bootstrap.css' rel='stylesheet'>
					<link href='css/signin.css' rel='stylesheet'>

				</head>

				<body>
					<div class='container'>
						$body
						<p>$timeString</p>
					</div>
				</body>

			</html>";

	}
}