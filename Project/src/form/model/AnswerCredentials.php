<?php

namespace form\model;

class AnswerCredentials {

	private $order;

	private $title;

	private function __construct($title, $type, $order = 1) {
		$this->title = $title;
		$this->order = $order;
		$this->type = $type;
	}

	public static function createFormBasic($title, $type, $order) {
		return new \form\model\AnswerCredentials($title, $type, $order);
	}

	public static function createFormFromDB($title, $order) {
		return new \form\model\AnswerCredentials($title, $order);
	}

	public function getTitle() {
		return $this->title;
	}

	public function getOrder() {
		return $this->order;
	}

	public function getType() {
		return $this->type;
	}
}