<?php

namespace form\model;

class QuestionCredentials {

	private $id;

	private $title;

	private $description;

	private $answers = array();

	private function __construct($title, $description, $id = null) {
		$this->title = $title;
		$this->description = $description;
		$this->id = $id;
	}

	public static function createFormBasic($title, $description) {
		return new \form\model\QuestionCredentials($title, $description);
	}

	public static function createFormFromDB($title, $description, $id) {
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

	public function getAnswers() {
		return $this->answers;
	}
	
	
}