<?php

namespace form\model;

class AnswerResultCredentials {
	
	private $answerId;

	private $amount;

	private $answerText;

	public function __construct($answerId, $amount, $answerText) {
		$this->answerId = $answerId;
		$this->amount = $amount;
		$this->answerText = $answerText;
	}

	public function getAmount() {
		return $this->amount;
	}

	public function getText() {
		return $this->answerText;
	}
}