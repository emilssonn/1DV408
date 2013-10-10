<?php

require_once(dirname(__FILE__)."/../config.php");
require_once("./src/controller/Application.php");

session_set_cookie_params(0, "/1DV408/LoginApp", "", false, true);
session_start();

$mysqli = new \mysqli($dbServer, $dbUser, $dbPassword, $db);

$application = new \controller\Application($mysqli);

$html = $application->runApplication();

echo $html;