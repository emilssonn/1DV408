<?php

namespace form\model;

require_once("./src/form/model/exception/TitleLength.php");
require_once("./src/form/model/exception/DescriptionLength.php");
require_once("./src/form/model/exception/DateError.php");

/**
 * @author Peter Emilsson
 * Represents a form, do not need to be a complete representation 
 */
class Form {

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
	 * @var \common\model\CustomDateTime
	 */
	private $endDate;

	/**
	 * @var \common\model\CustomDateTime
	 */
	private $createdDate;

	/**
	 * @var \common\model\CustomDateTime
	 */
	private $lastUpdateDate;

	/**
	 * @var \user\model\UserCredentials
	 */
	private $author;

	/**
	 * @var bool
	 */
	private $isPublished;

	/**
	 * @var integer
	 */
	CONST MinStringLength = 5;

	/**
	 * @var integer
	 */
	CONST MaxTitleLength = 100;

	/**
	 * @var integer
	 */
	CONST MaxDescriptionLength = 600;

	/**
	 * Simple Regex for matching format of date, do not check what number, only amount of numbers
	 * @var string
	 */
	CONST DatePattern = "/^\d{4}-\d{2}-\d{2}\s{1}\d{2}:\d{2}$/";

	/**
	 * @var array of \form\model\QuestionCredentials
	 */
	private $questionCredArray = array();

	/**
	 * @param string  						$title        
	 * @param string  						$description   
	 * @param \common\model\CustomDateTime	$endDate      
	 * @param \user\model\UserCredentials  	$author, not required       
	 * @param int  							$id, not required                
	 * @param \common\model\CustomDateTime  $createdDate, not required        
	 * @param \common\model\CustomDateTime  $lastUpdateDate, not required    
	 * @param boolean 						$isPublished, not required        
	 */
	private function __construct($title, $description, $endDate, $author = null, $id = null, $createdDate = null, $lastUpdateDate = null, $isPublished = false) {
		$this->title = $title;
		$this->description = $description;
		$this->endDate = $endDate;
		$this->author = $author;
		$this->createdDate = $createdDate;
		$this->lastUpdateDate = $lastUpdateDate;
		$this->id = $id;
		$this->isPublished = $isPublished;
	}

	/**
	 * Create a basic form, used by views
	 * @param  string $title     
	 * @param  string $description 
	 * @param  string $endDate     
	 * @return form\model\Form
	 */
	public static function createBasic($title, $description, $endDate) {
		self::validateTitle($title);
		self::validateDescription($description);
		self::validateEndDate($endDate);
		return new \form\model\Form($title, $description, new \common\model\CustomDateTime($endDate));
	}

	/**
	 * Create a full form, user by DAL classes
	 * @param  string 						$title         
	 * @param  string 						$description 
	 * @param  \common\model\CustomDateTime $endDate        
	 * @param  \user\model\UserCredentials 	$author         
	 * @param  \common\model\CustomDateTime $createdDate    
	 * @param  \common\model\CustomDateTime $lastUpdateDate 
	 * @param  int 							$id           
	 * @param  bool 						$published     
	 * @return \form\model\Form              
	 */
	public static function createFull($title, $description, $endDate, $author, $createdDate, $lastUpdateDate, $id, $published) {
		return new \form\model\Form($title, $description, $endDate, $author, $id, $createdDate, $lastUpdateDate, $published);
	}

	/**
	 * Create a simple form for listing, used by DAL classes
	 * @param  string $title    
	 * @param  string $description 
	 * @param  \common\model\CustomDateTime $endDate    
	 * @param  \user\model\UserCredentials $author      
	 * @param  int $id         
	 * @return \form\model\Form           
	 */
	public static function createSimple($title, $description, $endDate, $author, $id) {
		return new \form\model\Form($title, $description, $endDate, $author, $id);
	}

	/**
	 * @param \form\model\QuestionCredentials $qCred
	 */
	public function addQuestion(\form\model\QuestionCredentials $qCred) {
		$this->questionCredArray[] = $qCred;
	}

	/**
	 * @param array of \form\model\QuestionCredentials $qCredArray
	 */
	public function addQuestions($qCredArray) {
		$this->questionCredArray = array_merge($this->questionCredArray, $qCredArray);
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
	 * @return \common\model\CustomDateTime
	 */
	public function getEndDate() {
		return $this->endDate;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return \common\model\CustomDateTime
	 */
	public function getCreatedDate() {
		return $this->createdDate;
	}

	/**
	 * @return \common\model\CustomDateTime
	 */
	public function getLastUpdatedDate() {
		return $this->lastUpdateDate;
	}

	/**
	 * @return \user\model\UserCredentials
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * @param int $id
	 * @throws \Exception If id is already set or if provided id is not a int
	 */
	public function setId($id) {
		if ($this->id != null && is_numeric($id)) {
			throw new \Exception('Id already set or invalid type');
		} else {
			$this->id = $id;
		}
	}

	/**
	 * @return boolean
	 */
	public function isPublished() {
		return $this->isPublished;
	}

	/**
	 * @return array of \form\model\QuestionCredentials
	 */
	public function getQuestions() {
		return $this->questionCredArray;
	}

	/**
	 * @return boolean
	 */
	public function isActive() {
		return !$this->endDate->hasPassed();
	}

	/**
	 * @param  string $title 
	 * @throws \form\model\exception\TitleLength If title do not validate
	 */
	private static function validateTitle($title) {
		if (strlen($title) < self::MinStringLength ||
			strlen($title) > self::MaxTitleLength) {
			throw new \form\model\exception\TitleLength(self::MinStringLength, self::MaxTitleLength);
		}
	}

	/**
	 * @param  string $description 
	 * @throws \form\model\exception\DescriptionLength If description do not validate
	 */
	private static function validateDescription($description) {
		if (strlen($description) < self::MinStringLength ||
			strlen($description) > self::MaxDescriptionLength) {
			throw new \form\model\exception\DescriptionLength(self::MinStringLength, self::MaxDescriptionLength);
		}
	}

	/**
	 * @param  string $endDate
	 * @throws \form\model\exception\DateError If date do not validate
	 */
	private static function validateEndDate($endDate) {
		if (preg_match(self::DatePattern, $endDate) !== 1 ||
			strtotime($endDate) < time()) {
			throw new \form\model\exception\DateError();
		}
	}
}