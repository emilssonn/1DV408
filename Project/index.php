<?php

require_once(dirname(__FILE__)."/../projectConfig.php");
require_once("./src/application/controller/Application.php");
require_once("./src/common/view/PageView.php");

session_set_cookie_params(0, "/1DV408/Project", "", false, true);
session_start();

$mysqli = new \mysqli($dbServer, $dbUser, $dbPassword, $db);

$application = new \application\controller\Application($mysqli);
$pageView = new \common\view\PageView();


$page = $application->runApplication();

echo $pageView->GetHTMLPage($page);