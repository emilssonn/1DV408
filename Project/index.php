<?php

require_once(dirname(__FILE__)."/../projectConfig.php");
require_once("./src/application/controller/Application.php");
require_once("./src/common/view/PageView.php");

session_set_cookie_params(0, "/1DV408/Project", "", false, true);
session_start();

$dbConnection = \common\model\DbConnection::getInstance();
$dbConnection->connect($dbServer, $dbUser, $dbPassword, $db);
//$mysqli = new \mysqli($dbServer, $dbUser, $dbPassword, $db);

$pageView = new \common\view\PageView();
$application = new \application\controller\Application($pageView);



$page = $application->run();

$dbConnection->close();

echo $pageView->getHTML($page);