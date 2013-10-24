<?php

namespace home\controller;

class Home {
	
	private $homeView;

	private $user;

	public function __construct(\authorization\model\UserCredentials $user,
								\home\view\Home $homeView) {
		$this->homeView = $homeView;
		$this->user = $user;
	}

	public function runHome() {

	}
}