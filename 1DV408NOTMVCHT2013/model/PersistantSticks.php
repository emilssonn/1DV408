<?php

namespace model;

class PersistantSticks {
	private static $sticks = "PersistantSticks::NumberOfSticks";

	/**
	 * Make sure we have a session
	 * @param integer $maxAmount
	 */
	public function __construct($maxAmount) {
		assert(isset($_SESSION));
		
		if (isset($_SESSION[self::$sticks]) == false) {
			$this->newGame($maxAmount);
		}
		
	}

	/**
	 * @param  integer $maxAmount 
	 */
	public function newGame($maxAmount) {
		$_SESSION[self::$sticks] = $maxAmount;
	}

	/**
	 * @return int 
	 */
	public function getNumberOfSticks() {
		return $_SESSION[self::$sticks];
	}

	/**
	 * We can only remove 1-3 sticks 
	 * Cannot remove more than we have
	 * 
	 * @param  \model\StickSelection $selection
	 */
	public function removeSticks(StickSelection $selection) {
		assert($selection->getAmount() >= 1);
		assert($selection->getAmount() <= 3);
		assert($selection->getAmount() < $_SESSION[self::$sticks]);

		$_SESSION[self::$sticks] -= $selection->getAmount();
	}
}