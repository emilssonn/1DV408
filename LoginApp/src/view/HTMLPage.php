<?php

namespace View;

require_once("./src/model/Time.php");

class HTMLPage {

	/**
	 * @param  String $title, page title
	 * @param  String $body, HTML body
	 * @return HTML, string of full page HTML
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

				</head>

				<body>
					<div>
						<h1>Laborationskod pe222bz</h1>
						$body
						<hr/>
						<p>$timeString</p>
					</div>
				</body>

			</html>";

	}
}