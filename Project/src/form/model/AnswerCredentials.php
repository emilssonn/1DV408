<?php

namespace form\model;

require_once("./src/form/model/exception/AnswerLength.php");

/**
 * @author Peter Emilsson
 * Represents a template answer
 */
class AnswerCredentials {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var int
	 */
	private $order;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var integer
	 */
	CONST MinStringLength = 2;

	/**
	 * @var integer
	 */
	CONST MaxStringLength = 100;

	/**
	 * @param string $title 
	 * @param string $type  
	 * @param int $order 
	 * @param int $id   
	 */
	private function __construct($title, $type, $order, $id = null) {
		$this->validateTitle($title);
		$this->title = $title;
		$this->order = $order;
		$this->type = $type;
		$this->id = $id;
	}

	/**
	 * @param  string $title 
	 * @param  string $type  
	 * @param  int $order 
	 * @return \form\model\AnswerCredentials
	 */
	public static function createBasic($title, $type, $order) {
		return new \form\model\AnswerCredentials($title, $type, $order);
	}

	/**
	 * @param  string $title 
	 * @param  string $type  
	 * @param  int $order 
	 * @param  int $id
	 * @return \form\model\AnswerCredentials
	 */
	public static function createFull($title, $type, $order, $id) {
		return new \form\model\AnswerCredentials($title, $type, $order, $id);
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return int
	 */
	public function getOrder() {
		return $this->order;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return int id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param  string $title
	 * @throws \form\model\exception\AnswerLength If title do not pass the valdiation
	 */
	private function validateTitle($title) {
		if (strlen($title) < self::MinStringLength ||
			strlen($title) > self::MaxStringLength) {
			throw new \form\model\exception\AnswerLength(self::MinStringLength, self::MaxStringLength);
		}
	}
}