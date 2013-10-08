<?php

require_once("./src/view/HTMLPage.php");
require_once("./src/controller/Login.php");
require_once("./src/model/UserDAL.php");

session_set_cookie_params(0, "/1DV408/LoginApp", "", false, true);
session_start();

$mysqli = new \mysqli("localhost", "root", "", "1dv408-lab");

$userDAL = new \model\UserDAL($mysqli);

$loginController = new \controller\Login($userDAL);

$html = $loginController->userAction();
$title = $loginController->getPageTitle();

$pageView = new \view\HTMLPage();
echo $pageView->getHTML($title, $html);