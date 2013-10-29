<?php

namespace form\model;

class UserQuestionCredentials {

	private $submittedDate;

	private $lastUpdatedDate;

	private $answersResultArray = array();

	public function __construct($submittedDate, $lastUpdatedDate) {
		$this->submittedDate = $submittedDate;
		$this->lastUpdatedDate = $lastUpdatedDate;
	}

	public function addAnswersResult($answersResultArray) {
		$this->answersResultArray = array_merge($this->answersResultArray, $answersResultArray);
	}

	public function getAnswersResult() {
		return $this->answersResultArray;
	}
}