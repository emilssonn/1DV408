<?php

namespace form\model;

class AnswerViewCredentials {

	private $questionId;

	private $answerId;

	public function __construct($qId, $aId) {
		$this->questionId = $qId;
		$this->answerId = $aId;
	}

	public function getQuestionId() {
		return $this->questionId;
	}

	public function getAnswerId() {
		return $this->answerId;
	}
}