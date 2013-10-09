<?php

namespace model;

require_once("model/StickSelection.php");
require_once("model/AIPlayer.php");
require_once("model/PersistantSticks.php");

class LastStickGame {

	/**
	 * @var integer
	 */
	private static $startingNumberOfSticks = 22;

	public function __construct() {
		$this->ai = new AIPlayer();
		$this->sticks = new PersistantSticks(self::$startingNumberOfSticks);
	}

	/**
	 * @return integer
	 */
	public function getStartingNumberOfSticks() {
		return self::$startingNumberOfSticks;
	}

	/**
	 * @param  \model\StickSelection    $playerSelection 
	 * @param  \model\StickGameObserver $observer        
	 */
	public function playerSelectsSticks(StickSelection $playerSelection, StickGameObserver $observer) {
		$this->sticks->removeSticks($playerSelection);

		if ($this->isGameOver()) {
			$observer->playerWins();
		} else {
			$this->AIPlayerTurn($observer);
		} 
	}	

	/**
	 * @param \model\StickGameObserver $observer
	 */
	private function AIPlayerTurn(StickGameObserver $observer) {
		$sticksLeft = $this->getNumberOfSticks();
		$selection = $this->ai->getSelection($sticksLeft, $observer);
		
		$this->sticks->removeSticks($selection);
		$observer->aiRemoved($selection);

		if ($this->isGameOver()) {
			$observer->playerLoose();
		}
	}

	/** 
	* @return boolean
	*/
	public function isGameOver() {
		return $this->sticks->getNumberOfSticks() < 2;
	}

	/** 
	* @return int
	*/
	public function getNumberOfSticks() {
		return $this->sticks->getNumberOfSticks();
	}

	public function newGame() {
		$this->sticks->newGame(self::$startingNumberOfSticks);
	}
}