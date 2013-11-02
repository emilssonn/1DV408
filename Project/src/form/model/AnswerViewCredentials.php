<?php

namespace form\model;

require_once("./src/common/model/exception/StringLength.php");

/**
 * @author Peter Emilsson
 * Represents the user submitted answer for one question
 */
class AnswerViewCredentials {

	/**
	 * @var int
	 */
	private $answerId;

	/**
	 * @var string
	 */
	private $noteText;

	/**
	 * @var integer
	 */
	CONST MinStringLength = 2;

	/**
	 * @var integer
	 */
	CONST MaxStringLength = 100;

	/**
	 * @param int $aId      
	 * @param string $noteText, not required
	 */
	public function __construct($aId, $noteText = null) {
		if ($noteText !== null)
			$this->validateNoteText($noteText);
		$this->answerId = $aId;
		$this->noteText = $noteText;
	}

	/**
	 * @return int
	 */
	public function getAnswerId() {
		return $this->answerId;
	}

	/**
	 * @return string
	 */
	public function getNoteText() {
		return $this->noteText;
	}

	/**
	 * Compare question id and answer id
	 * @param  int $qId 
	 * @param  int $aId 
	 * @return bool
	 */
	public function compare($qId, $aId) {
		return $qId == $this->questionId && $aId == $this->answerId;
	}

	/**
	 * @param  string $noteText
	 * @throws \form\model\exception\StringLength If note text do not pass the valdiation
	 */
	private function validateNoteText($noteText) {
		if (strlen($noteText) < self::MinStringLength ||
			strlen($noteText) > self::MaxStringLength) {
			throw new \common\model\exception\StringLength(self::MinStringLength, self::MaxStringLength);
		}
	}
}