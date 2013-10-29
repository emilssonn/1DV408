<?php

namespace form\model;

class AnswerViewCredentials {

	private $id;

	private $questionId;

	private $answerId;

	private $noteText;

	private function __construct($qId, $aId, $id = null, $noteText = null) {
		$this->questionId = $qId;
		$this->answerId = $aId;
		$this->id = $id;
		$this->noteText = $noteText;
	}

	public static function createBasic($qId, $aId) {
		return new \form\model\AnswerViewCredentials($qId, $aId);
	}

	public static function createFull($id, $qId, $aId, $noteText) {
		return new \form\model\AnswerViewCredentials($qId, $aId, $id, $noteText);
	}

	public function getQuestionId() {
		return $this->questionId;
	}

	public function getAnswerId() {
		return $this->answerId;
	}

	public function getId() {
		return $this->id;
	}

	public function compare($qId, $aId) {
		return $qId == $this->questionId && $aId == $this->answerId;
	}

	public function setId($id) {
		if ($this->id != null) {
			throw new \Exception('Id already set');
		} else {
			$this->id = $id;
		}
	}
}