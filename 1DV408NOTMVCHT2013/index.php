<?php

session_start();

require_once("controller/PlayGame.php");
require_once("view/HTMLPage.php");

$controller = new controller\PlayGame();


$body = $controller->runGame();


$page = new view\HTMLPage();

//@error, string should be in a view?
echo $page->getPage("Game of sticks", $body);




