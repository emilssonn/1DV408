<?php

require_once("/src/view/HTMLPage.php");
require_once("/src/view/LoginView.php");
require_once("/src/controller/LoginController.php");
require_once("/src/model/UserModel.php");
require_once("/src/model/UserLogin.php");

session_start();

$userLogin = new \model\UserLogin();

$user = $userLogin->login();

$loginController = new \controller\LoginController($user, $userLogin);

$html = $loginController->checkUser();

$pageView = new \view\HTMLPage();
echo $pageView->getHTML("Logga in", $html);