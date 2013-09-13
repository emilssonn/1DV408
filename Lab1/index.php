<?php

require_once("/src/view/HTMLPage.php");
require_once("/src/view/Login.php");
require_once("/src/controller/Login.php");
require_once("/src/model/User.php");
require_once("/src/model/UserLogin.php");

session_start();

$userLogin = new \model\UserLogin();

$user = $userLogin->login();

$loginController = new \Controller\Login($user, $userLogin);

$html = $loginController->checkUser();

$pageView = new \view\HTMLPage();
echo $pageView->getHTML("Logga in", $html);