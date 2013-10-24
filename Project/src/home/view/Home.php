<?php

namespace home\view;

class Home {
	private $navigationView;

	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
	}

	public function getHTML() {
		$html = "
				<a href='?form&create'>Create new form</a>";

		return $html;
	}
}