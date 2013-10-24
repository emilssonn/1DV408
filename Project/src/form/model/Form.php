<?php

namespace form\model;

class Form {

	private $formCredentials;

	private $formQustions = array();

	public function __construct(\form\model\FormCredentials $formCred) {
		$this->formCredentials = $formCred;
	}

	public function addQuestion(\form\model\QuestionCredentials $question) {
		$this->formQuestion[] = $question;
	}

	public function getId() {
		return $this->formCredentials->getId();
	}

	public function getFormCredentials() {
		return $this->formCredentials;
	}
}