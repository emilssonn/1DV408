<?php

require_once("./src/view/HTMLPage.php");
require_once("./src/view/Login.php");
require_once("./src/controller/Login.php");
require_once("./src/model/User.php");
require_once("./src/model/SessionAuth.php");

session_start();

$sessionAuthModel = new \model\SessionAuth();

$user = $sessionAuthModel->load();

$loginController = new \Controller\Login($user, $sessionAuthModel);

$html = $loginController->userAction();
$title = $loginController->getPageTitle();

$pageView = new \view\HTMLPage();
echo $pageView->getHTML($title, $html);