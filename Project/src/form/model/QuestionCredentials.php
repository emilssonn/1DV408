<?php

namespace form\model;

class QuestionCredentials {

	private $id;

	private $title;

	private $description;

	private $answers = array();

	private static $minStringLength = 5;

	private static $maxTitleLength = 50;

	private static $maxDescription = 200;

	private function __construct($title, $description, $id = null) {
		$this->validateTitle($title);
		$this->validateDescription($description);
		$this->title = $title;
		$this->description = $description;
		$this->id = $id;
	}

	public static function createBasic($title, $description) {
		return new \form\model\QuestionCredentials($title, $description);
	}

	public static function createFull($title, $description, $id) {
		return new \form\model\QuestionCredentials($title, $description, $id);
	}

	public function getTitle() {
		return $this->title;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		if ($this->id != null) {
			throw new \Exception('Id already set');
		} else {
			$this->id = $id;
		}
	}

	public function addAnswer(\form\model\AnswerCredentials $answer) {
		$this->answers[] = $answer;
	}

	public function addAnswers($answers) {
		$this->answers = array_merge($this->answers, $answers);
	}

	public function getAnswers() {
		return $this->answers;
	}

	private function validateTitle($title) {
		if (strlen($title) < self::$minStringLength ||
			strlen($title) > self::$maxTitleLength) {
			throw new \Exception('Question title not valid');
		}
	}

	private function validateDescription($description) {
		if (strlen($description) < self::$minStringLength ||
			strlen($description) > self::$maxDescription) {
			throw new \Exception('Question description not valid');
		}
	}
	
	
}