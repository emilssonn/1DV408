<?php

namespace form\model;

/**
 * @author Peter Emilsson
 * Represents the full result of all submitted answers to a question
 */
class QuestionResultCredentials {

	/**
	 * @var array of \form\model\AnswerResultCredentials
	 */
	private $answersResultArray = array();

	/**
	 * @var int
	 */
	private $questionId;

	/**
	 * @var string
	 */
	private $questionTitle;

	/**
	 * @var string
	 */
	private $questionDescription;

	/**
	 * @param int $questionId         
	 * @param string $questionTitle      
	 * @param string $questionDescription 
	 */
	public function __construct($questionId, $questionTitle, $questionDescription) {
		$this->questionId = $questionId;
		$this->questionTitle = $questionTitle;
		$this->questionDescription = $questionDescription;
	}

	/**
	 * @param \form\model\AnswerResultCredentials $answerResultCred 
	 */
	public function addAnswerResult(\form\model\AnswerResultCredentials $answerCred) {
		$this->answersResultArray[] = $answerCred;
	} 

	/**
	 * @return array of \form\model\AnswerResultCredentials
	 */
	public function getAnswersResult() {
		return $this->answersResultArray;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->questionTitle;
	}

	/**
	 * @return string
	 */
	public function getQuestionDescription() {
		return $this->questionDescription;
	}
}