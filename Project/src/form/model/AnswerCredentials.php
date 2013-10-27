<?php

namespace form\model;

class AnswerCredentials {

	private $id;

	private $order;

	private $title;

	private static $minStringLength = 2;

	private static $maxStringLength = 50;

	private function __construct($title, $type, $order = 1, $id = null) {
		$this->validateTitle($title);
		$this->title = $title;
		$this->order = $order;
		$this->type = $type;
		$this->id = $id;
	}

	public static function createFormBasic($title, $type, $order) {
		return new \form\model\AnswerCredentials($title, $type, $order);
	}

	public static function createFormFromDB($title, $type, $order, $id) {
		return new \form\model\AnswerCredentials($title, $type, $order, $id);
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

	public function getId() {
		return $this->id;
	}

	private function validateTitle($title) {
		if (strlen($title) < self::$minStringLength ||
			strlen($title) > self::$maxStringLength) {
			throw new \Exception('Answer title not valid');
		}
	}
}