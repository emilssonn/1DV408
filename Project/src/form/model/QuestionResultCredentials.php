<?php

namespace form\model;

class QuestionResultCredentials {

	private $answersResultArray = array();

	private $questionId;

	private $questionTitle;

	private $questionDescription;

	public function __construct($questionId, $questionTitle, $questionDescription) {
		$this->questionId = $questionId;
		$this->questionTitle = $questionTitle;
		$this->questionDescription = $questionDescription;
	}

	public function addAnswerResult(\form\model\AnswerResultCredentials $answerCred) {
		$this->answersResultArray[] = $answerCred;
	} 

	public function getAnswersResult() {
		return $this->answersResultArray;
	}

	public function getTitle() {
		return $this->questionTitle;
	}

	public function getQuestionDescription() {
		return $this->questionDescription;
	}
}