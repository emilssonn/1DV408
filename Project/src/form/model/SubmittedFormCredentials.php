<?php

namespace form\model;

/**
 * @author Peter Emilsson
 * Represent a submitted form by one user, with results
 */
class SubmittedFormCredentials {

	/**
	 * @var int
	 */
	private $formId;

	/**
	 * @var int
	 */
	private $userFormId;

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
	private $submittedDate;

	/**
	 * @var \common\model\CustomDateTime
	 */
	private $lastUpdateDate;

	/**
	 * @var \user\model\UserCredentials
	 */
	private $author;

	/**
	 * @var array of \form\model\QuestionViewCredentials
	 */
	private $answersResultArray = array();

	/**
	 * @param int                    	   $formId         
	 * @param int                    	   $userFormId    
	 * @param string               		   $title          
	 * @param string                   	   $description   
	 * @param \user\model\UserCredentials  $author        
	 * @param \common\model\CustomDateTime $endDate        
	 * @param \common\model\CustomDateTime $submittedDate 
	 * @param \common\model\CustomDateTime $lastUpdateDate 
	 */
	public function __construct($formId, $userFormId, $title, $description, \user\model\UserCredentials $author, 
								\common\model\CustomDateTime $endDate, 
								\common\model\CustomDateTime $submittedDate, 
								\common\model\CustomDateTime $lastUpdateDate) {
		$this->title = $title;
		$this->description = $description;
		$this->endDate = $endDate;
		$this->author = $author;
		$this->submittedDate = $submittedDate;
		$this->lastUpdateDate = $lastUpdateDate;
		$this->formId = $formId;
		$this->userFormId = $userFormId;
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
	public function getFormId() {
		return $this->formId;
	}

	/**
	 * @return int
	 */
	public function getUserFormId() {
		return $this->userFormId;
	}

	/**
	 * @return \common\model\CustomDateTime
	 */
	public function getSubmittedDate() {
		return $this->submittedDate;
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
	 * @param array of \form\model\QuestionViewCredentials $answersResultArray 
	 */
	public function addAnswersResult($answersResultArray) {
		$this->answersResultArray = array_merge($this->answersResultArray, $answersResultArray);
	}

	/**
	 * @return array of \form\model\QuestionViewCredentials
	 */
	public function getAnswersResult() {
		return $this->answersResultArray;
	}
}