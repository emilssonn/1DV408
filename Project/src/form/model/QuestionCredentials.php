<?php

namespace form\model;

require_once("./src/form/model/exception/TitleLength.php");
require_once("./src/form/model/exception/DescriptionLength.php");

/**
 * @author Peter Emilsson
 * Represnts a template question
 */
class QuestionCredentials {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var bool
	 */
	private $required;

	/**
	 * @var bool
	 */
	private $commentText;

	/**
	 * @var array of \form\model\AnswerCredentials
	 */
	private $answerCredentialsArray = array();

	/**
	 * @var integer
	 */
	CONST MinStringLength = 5;

	/**
	 * @var integer
	 */
	CONST MaxTitleLength = 200;

	/**
	 * @var integer
	 */
	CONST MaxDescriptionLength = 600;

	/**
	 * @param string $title      
	 * @param string $description 
	 * @param bool $required   
	 * @param bool $commentText
	 * @param int $id         
	 */
	private function __construct($title, $description, $required, $commentText, $id = null) {
		$this->validateTitle($title);
		$this->validateDescription($description);
		$this->title = $title;
		$this->description = $description;
		$this->required = $required;
		$this->commentText = $commentText;
		$this->id = $id;
	}

	/**
	 * @param  string $title       
	 * @param  string $description 
	 * @param  bool $required    
	 * @param  bool $commentText 
	 * @return \form\model\QuestionCredentials
	 */
	public static function createBasic($title, $description, $required, $commentText) {
		return new \form\model\QuestionCredentials($title, $description, $required, $commentText);
	}

	/**
	 * @param  string $title       
	 * @param  string $description 
	 * @param  int $id          
	 * @param  bool $required    
	 * @param  bool $commentText 
	 * @return \form\model\QuestionCredentials
	 */
	public static function createFull($title, $description, $id, $required, $commentText) {
		return new \form\model\QuestionCredentials($title, $description, $required, $commentText, $id);
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return boolean
	 */
	public function isRequired() {
		return $this->required;
	}

	/**
	 * @return bool
	 */
	public function commentText() {
		return $this->commentText;
	}

	/**
	 * @param int $id
	 * @throws \Exception If if id is allready set or not int
	 */
	public function setId($id) {
		if ($this->id != null) {
			throw new \Exception('Id already set');
		} else {
			$this->id = $id;
		}
	}

	/**
	 * @param  int $aId 
	 * @return \form\model\AnswerCredentials
	 * @throws \Exception If no answer is found
	 */
	public function getAnswerById($aId) {
		foreach ($this->answerCredentialsArray as $answer) {
			if ($answer->getId() == $aId)
				return $answer;
		}
		throw new \Exception("Answer not found");
	}

	/**
	 * @param \form\model\AnswerCredentials $answer
	 */
	public function addAnswer(\form\model\AnswerCredentials $answer) {
		$this->answerCredentialsArray[] = $answer;
	}

	/**
	 * @param array of \form\model\AnswerCredentials $aCredArray
	 */
	public function addAnswers($aCredArray) {
		$this->answerCredentialsArray = array_merge($this->answerCredentialsArray, $aCredArray);
	}

	/**
	 * @return array of \form\model\AnswerCredentials
	 */
	public function getAnswers() {
		return $this->answerCredentialsArray;
	}

	/**
	 * @param  string $title
	 * @throws \form\model\exception\TitleLength If title do not validate
	 */
	private function validateTitle($title) {
		if (strlen($title) < self::MinStringLength ||
			strlen($title) > self::MaxTitleLength) {
			throw new \form\model\exception\TitleLength(self::MinStringLength, self::MaxTitleLength);
		}
	}

	/**
	 * @param  string $description 
	 * @throws \form\model\exception\DescriptionLength If description do not validate
	 */
	private function validateDescription($description) {
		if (!empty($description) && 
			(strlen($description) < self::MinStringLength ||
			strlen($description) > self::MaxDescriptionLength)) {
			throw new \form\model\exception\DescriptionLength(self::MinStringLength, self::MaxDescriptionLength);
		}
	}
}