<?php

namespace user\model;

/**
 * @author Daniel Toll - https://github.com/dntoll
 * Changes by Peter Emilsson
 */
abstract class TemporaryPassword {
	/**
	 * @var String
	 */
	protected $temporaryPassword;


	public function getTemporaryPassword() {
		return $this->temporaryPassword;
	}
}