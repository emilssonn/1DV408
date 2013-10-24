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

	private function __construct($title, $description, $endDate, $authorId = null, $id = null, $createdDate = null, $lastUpdateDate = null) {
		$this->title = $title;
		$this->description = $description;
		$this->endDate = $endDate;
		$this->authorId = $authorId;
		$this->createdDate = $createdDate;
		$this->lastUpdateDate = $lastUpdateDate;
		$this->id = $id;
	}

	public static function createFormBasic($title, $description, $endDate) {
		return new \form\model\FormCredentials($title, $description, $endDate);
	}

	public static function createFormFromDB($title, $description, $endDate, $authorId, $createdDate, $lastUpdateDate, $id) {
		return new \form\model\FormCredentials($title, $description, $endDate, $authorId, $id, $createdDate, $lastUpdateDate);
	}

	public static function createFromDbSimple($title, $description, $endDate, $authorId, $id) {
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

	public function setId($id) {
		if ($this->id != null) {
			throw new \Exception('Id already set');
		} else {
			$this->id = $id;
		}
	}
	
	
}