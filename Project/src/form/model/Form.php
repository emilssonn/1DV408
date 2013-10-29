<?php

namespace form\model;

class Form {

	private $formCredentials;

	private $formQuestions = array();

	public function __construct(\form\model\FormCredentials $formCred) {
		$this->formCredentials = $formCred;
	}

	public function addQuestion(\form\model\QuestionCredentials $question) {
		$this->formQuestions[] = $question;
	}

	public function addQuestions($questions) {
		$this->formQuestions = array_merge($this->formQuestions, $questions);
	}

	public function getId() {
		return $this->formCredentials->getId();
	}

	public function getFormCredentials() {
		return $this->formCredentials;
	}

	public function getQuestions() {
		return $this->formQuestions;
	}

	public function isActive() {
		return strtotime($this->formCredentials->getEndDate()) > time();
	}
}