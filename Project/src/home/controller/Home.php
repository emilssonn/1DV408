<?php

namespace home\controller;

require_once("./src/common/controller/IController.php");

class Home implements \common\controller\IController {
	
	private $homeView;

	private $user;

	public function __construct(\authorization\model\UserCredentials $user,
								\home\view\Home $homeView) {
		$this->homeView = $homeView;
		$this->user = $user;
	}

	public function run() {

	}
}