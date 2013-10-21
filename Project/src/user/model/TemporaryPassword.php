<?php

namespace authorization\model;

abstract class TemporaryPassword {
	/**
	 * @var String
	 */
	protected $temporaryPassword;


	public function getTemporaryPassword() {
		return $this->temporaryPassword;
	}
}