<?php

namespace common\model;

/**
 * @author Peter Emilsson
 * Extends the standard \DateTime object
 * Adds formated __toString and hasPassed method
 */
class CustomDateTime extends \DateTime {

    /**
     * Return date in yyyy-mm-dd hh:mm format
     * @return String
     */
    public function __toString() {
        return $this->format('Y-m-d H:i');
    }

    /**
     * Check if $this date has passed the current date
     * @return boolean
     */
    public function hasPassed() {
    	return $this < new \DateTime();
    }

}