<?php

namespace form\model;

class FormCredentials {

	private $id;

	private $title;

	private $description;

	private $endDate;

	private $createdDate;

	private $lastUpdateDate;

	private $authorId;

	private $isPublished;

	private static $minStringLength = 5;

	private static $maxTitleLength = 50;

	private static $maxDescription = 200;

	private function __construct($title, $description, $endDate, $authorId = null, $id = null, $createdDate = null, $lastUpdateDate = null, $isPublished = false) {
		$this->validateTitle($title);
		$this->validateDescription($description);
		$this->title = $title;
		$this->description = $description;
		$this->endDate = $endDate;
		$this->authorId = $authorId;
		$this->createdDate = $createdDate;
		$this->lastUpdateDate = $lastUpdateDate;
		$this->id = $id;
		$this->isPublished = $isPublished;
	}

	public static function createBasic($title, $description, $endDate) {
		return new \form\model\FormCredentials($title, $description, $endDate);
	}

	public static function createFull($title, $description, $endDate, $authorId, $createdDate, $lastUpdateDate, $id, $published = false) {
		return new \form\model\FormCredentials($title, $description, $endDate, $authorId, $id, $createdDate, $lastUpdateDate, $published);
	}

	public static function createSimple($title, $description, $endDate, $authorId, $id) {
		return new \form\model\FormCredentials($title, $description, $endDate, $authorId, $id);
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

	public function getId() {
		return $this->id;
	}

	public function getCreatedDate() {
		return $this->createdDate;
	}

	public function getLastUpdatedDate() {
		return $this->lastUpdateDate;
	}

	public function getAuthorId() {
		return $this->authorId;
	}

	public function setId($id) {
		if ($this->id != null) {
			throw new \Exception('Id already set');
		} else {
			$this->id = $id;
		}
	}

	public function isPublished() {
		return $this->isPublished;
	}

	private function validateTitle($title) {
		if (strlen($title) < self::$minStringLength ||
			strlen($title) > self::$maxTitleLength) {
			throw new \Exception('Form title not valid');
		}
	}

	private function validateDescription($description) {
		if (strlen($description) < self::$minStringLength ||
			strlen($description) > self::$maxDescription) {
			throw new \Exception('Form description not valid');
		}
	}
	
	
}