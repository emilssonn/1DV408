<?php

namespace form\model;

class SubmittedFormCredentials {

	private $formId;

	private $userFormId;

	private $title;

	private $description;

	private $endDate;

	private $submittedDate;

	private $lastUpdateDate;

	private $authorId;

	private $answersResultArray = array();

	public function __construct($formId, $userFormId, $title, $description, $authorId, $endDate, $submittedDate, $lastUpdateDate) {
		$this->title = $title;
		$this->description = $description;
		$this->endDate = $endDate;
		$this->authorId = $authorId;
		$this->submittedDate = $submittedDate;
		$this->lastUpdateDate = $lastUpdateDate;
		$this->formId = $formId;
		$this->userFormId = $userFormId;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getEndDate() {
		return $this->endDate;
	}

	public function getFormId() {
		return $this->formId;
	}

	public function getUserFormId() {
		return $this->userFormId;
	}

	public function getSubmittedDate() {
		return $this->submittedDate;
	}

	public function getLastUpdatedDate() {
		return $this->lastUpdateDate;
	}

	public function getAuthorId() {
		return $this->authorId;
	}

	public function addAnswersResult($answersResultArray) {
		$this->answersResultArray = array_merge($this->answersResultArray, $answersResultArray);
	}

	public function getAnswersResult() {
		return $this->answersResultArray;
	}
}