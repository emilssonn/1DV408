<?php

namespace home\controller;

require_once("./src/common/controller/IController.php");
require_once("./src/home/view/Home.php");

/**
 * @author Peter Emilsson
 */
class Home implements \common\controller\IController {
	
	/**
	 * @var \home\view\Home
	 */
	private $homeView;

	/**
	 * @var \user\model\UserCredentials
	 */
	private $user;

	/**
	 * @param \user\model\UserCredentials  $user          
	 * @param \application\view\Navigation $navigationView 
	 */
	public function __construct(\user\model\UserCredentials $user,
								\application\view\Navigation $navigationView) {
		$this->homeView = new \home\view\Home($navigationView);
		$this->user = $user;
	}

	/**
	 * @return string HTML
	 */
	public function run() {
		return $this->homeView->getHTML();
	}
}