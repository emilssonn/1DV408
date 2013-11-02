<?php

namespace form\model;

/**
 * @author Peter Emilsson
 * Collection to many handle \form\model\Form
 * Implements \Iterator
 * Can be used like an array in foreach
 */
class FormCollection implements \Iterator {

    /**
     * @var array if \form\model\Form
     */
	private $forms = array();

    /**
     * @param array of \form\model\Form $forms
     */
	public function __construct($forms = null) {
		if (is_array($forms)) {
            $this->forms = $forms;
        }	
	}

    /**
     * @return array of \form\model\Form
     */
	public function getPublishedAndActive() {
		$forms = array_filter(
    		$this->forms,
    		function ($form) use (&$id) {
                $endDate = $form->getEndDate();
    			return $form->isPublished() &&
    				!$endDate->hasPassed();
    		}
		);
		return $forms;
	}

    /**
     * @return array of \form\model\Form
     */
	public function getEnded() {
		$forms = array_filter(
    	$this->forms,
    		function ($form) use (&$id) {
                $endDate = $form->getEndDate();
    			return $endDate->hasPassed();
    		}
		);
		return $forms;
	}

    /**
     * @return array of \form\model\Form
     */
	public function getNotPublished() {
		$forms = array_filter(
    		$this->forms,
    		function ($form) use (&$id) {
                $endDate = $form->getEndDate();
    			return !$endDate->hasPassed() &&
    					!$form->isPublished();
    		}
		);
		return $forms;
	}

    /**
     * @param \form\model\Form $form
     */
	public function addForm(\form\model\Form $form) {
		$this->forms[] = $form;
	}

    /**
     * @return array of \form\model\Form
     */
	public function getForms() {
		return $this->forms;
	}

    /**
     * @return int
     */
    public function count() {
        return count($this->forms);
    }

	/**
	 * Source: http://php.net/manual/en/language.oop5.iterations.php
	 */
    public function rewind() {
        reset($this->forms);
    }
  
    public function current() {
        $forms = current($this->forms);
        return $forms;
    }
  
    public function key() {
        $forms = key($this->forms);
        return $forms;
    }
  
    public function next() {
        $forms = next($this->forms);
        return $forms;
    }
  
    public function valid() {
        $key = key($this->forms);
        $forms = ($key !== NULL && $key !== FALSE);
        return $forms;
    }

}