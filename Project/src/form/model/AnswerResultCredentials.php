<?php

namespace form\model;

/**
 * @author Peter Emilsson
 * Represent the result of all submissions for one answer
 */
class AnswerResultCredentials {
	
	/**
	 * @var string
	 */
	private $answerId;

	/**
	 * @var int
	 */
	private $amount;

	/**
	 * @var string
	 */
	private $answerText;

	/**
	 * @param int $answerId   
	 * @param int $amount    
	 * @param string $answerText 
	 */
	public function __construct($answerId, $amount, $answerText) {
		$this->answerId = $answerId;
		$this->amount = $amount;
		$this->answerText = $answerText;
	}

	/**
	 * @return int
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * @return string
	 */
	public function getText() {
		return $this->answerText;
	}
}