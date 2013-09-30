<?php

require_once("./src/view/HTMLPage.php");
require_once("./src/controller/Login.php");


session_start();

$mysqli = new \mysqli("localhost", "root", "", "1dv408-lab");

$loginController = new \Controller\Login($mysqli);

$html = $loginController->userAction();
$title = $loginController->getPageTitle();

$pageView = new \view\HTMLPage();
echo $pageView->getHTML($title, $html);