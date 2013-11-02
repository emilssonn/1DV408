<?php

namespace form\model;

/**
 * @author Peter Emilsson
 * Represents the submitted answer of one question from one user
 */
class QuestionViewCredentials {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var int
	 */
	private $questionId;

	/**
	 * @var string
	 */
	private $commentText;

	/**
	 * @var \form\model\AnswerViewCredentials
	 */
	private $aViewCred;

	/**
	 * @var int
	 */
	CONST MaxCommentLength = 1000;

	/**
	 * @param int                        		$qId         
	 * @param \form\model\AnswerViewCredentials $answer      
	 * @param string                         	$commentText 
	 * @param int                      			$id         
	 */
	private function __construct($qId, \form\model\AnswerViewCredentials $answer, 
									$commentText = null, $id = null) {
		if ($commentText !== null)
			$this->validateComment($commentText);
		$this->questionId = $qId;
		$this->id = $id;
		$this->commentText = $commentText;
		$this->aViewCred = $answer;
	}

	/**
	 * @param  int                       		 $qId         
	 * @param  \form\model\AnswerViewCredentials $answer      
	 * @param  string 							 $commentText 
	 * @return \form\model\QuestionViewCredentials
	 */
	public static function createBasic($qId, \form\model\AnswerViewCredentials $answer, $commentText = null) {
		return new \form\model\QuestionViewCredentials($qId, $answer, $commentText);
	}

	/**
	 * @param  int                        			$id          
	 * @param  int                         			$qId         
	 * @param  string                         		$commentText 
	 * @param  \form\model\AnswerViewCredentials 	$answer      
	 * @return \form\model\QuestionViewCredentials                                   
	 */
	public static function createFull($id, $qId, $commentText, \form\model\AnswerViewCredentials $answer) {
		return new \form\model\QuestionViewCredentials($qId, $answer, $commentText, $id);
	}

	/**
	 * @return int
	 */
	public function getQuestionId() {
		return $this->questionId;
	}

	/**
	 * @return string
	 */
	public function getCommentText() {
		return $this->commentText;
	}

	/**
	 * @return \form\model\AnswerViewCredentials
	 */
	public function getAnswer() {
		return $this->aViewCred;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 * @throws \Exception If id is set or not int
	 */
	public function setId($id) {
		if ($this->id != null && is_numeric($id)) {
			throw new \Exception('Id already set or invalid type');
		} else {
			$this->id = $id;
		}
	}

	/**
	 * Compare by Ids
	 * @param  int $qId 
	 * @param  int $aId
	 * @return bool
	 */
	public function compare($qId, $aId) {
		return $qId == $this->questionId && $aId == $this->aViewCred->getAnswerId();
	}

	private function validateComment($comment) {
		if (strlen($comment) > self::MaxCommentLength)
			throw new \Exception('Comment to long');
	}
}