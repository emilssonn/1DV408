<?php

namespace common\view;

class Page {
  
	//Properties of the document
	public $title = "";
	public $body = "";
	public $menu = "";
	public $navBar = "";

	public function __construct($title, $body, $menu = "", $navBar = "") {
		$this->title = $title;
		$this->menu = $menu;
		$this->body = $body;
		$this->navBar = $navBar;
	}
}

