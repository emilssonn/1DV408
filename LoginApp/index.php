<?php

require_once("./src/controller/Application.php");

session_set_cookie_params(0, "/1DV408/LoginApp", "", false, true);
session_start();

$application = new \controller\Application();

$html = $application->runApplication();

echo $html;