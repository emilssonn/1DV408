<?php

namespace common\model\exception;

/**
 * @author Peter Emilsson
 * Exception for string length error
 */
class StringLength extends \Exception {
    /**
     * @var int
     */
	private $minLength;

    /**
     * @var int
     */
	private $maxLength;

    /**
     * @param int $minLength 
     * @param int $maxLength 
     */
    public function __construct($minLength, $maxLength) {

    	$this->minLength = $minLength;
    	$this->maxLength = $maxLength;

        parent::__construct();
    }

    /**
     * @return int
     */
    public function getMinLength() {
    	return $this->minLength;
    }

    /**
     * @return int
     */
    public function getMaxLength() {
    	return $this->maxLength;
    }
}