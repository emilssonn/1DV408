<?php

namespace view;

require_once("model/StickGameObserver.php");

class GameView implements \model\StickGameObserver {

	/** 
	* @var integer
	*/
	private $numberOfSticksAIDrewLastTime = 0;

	/**
	 * @var string
	 */
	private static $drawGET = "draw";

	/**
	 * @var string
	 */
	private static $startOverGET = "startOver";

	/**
	 * @var string
	 */
	private $message;

	/** 
	* @var boolean
	*/
	private $playerWon = false;

	/**
	 * Player wins
	 */
	public function playerWins() {
		$this->playerWon = true;
	}

	/**
	 * PLayer loses
	 */
	public function playerLoose() {
		$this->playerWon = false;
	}

	/**
	 * Sets the number of sticks the AI player did
	 * @param  \model\StickSelection $sticks 
	 */
	public function aiRemoved(\model\StickSelection $sticks) {
		$this->numberOfSticksAIDrewLastTime = $sticks->getAmount();
	}

	/**
	 * Set the message
	 */
	public function aiBadDraw() {
		$this->message = "<p>AIPlayer - \"Grr...\" </p>";
	}

	/**
	 * Set the message
	 */
	public function aiGoodDraw() {
		$this->message = "<p>AIPlayer - \"Got you, you have already lost!!!\"</p>  ";
	}

	/**
	 * @param \model\LastStickGame $game 
	 */
	public function __construct(\model\LastStickGame $game) {
		$this->game = $game;
	}

	/** 
	* @return String HTML
	*/
	public function show() {
		if ($this->game->isGameOver()) {

			return 	$this->message .
					$this->showSticks() . 
					$this->showWinner() . 
					$this->startOver();
		} else {
			return 	$this->message .
					$this->showSticks() . 
					$this->showSelection();
		}
	}

	/** 
	* @return boolean
	*/
	public function playerSelectSticks() {
		return isset($_GET[self::$drawGET]);
	}

	/** 
	* @return boolean
	*/
	public function playerStartsOver() {
		return isset($_GET[self::$startOverGET]);
	}

	/** 
	* @return \model\StickSelection
	*/
	public function getNumberOfSticks() {
		switch ($_GET[self::$drawGET]) {
			case 1 : return \model\StickSelection::One(); break;
			case 2 : return \model\StickSelection::Two(); break;
			case 3 : return \model\StickSelection::Three(); break;
		}
		$this->message = "<h1>Unauthorized input</h1>";
		throw new \Exception("Invalid input");
	}

	/** 
	* @return String HTML
	*/
	private function showSticks() {
		$numSticks = $this->game->getNumberOfSticks();
		$aiDrew = $this->numberOfSticksAIDrewLastTime;
		$startingNumberOfSticks = $this->game->getStartingNumberOfSticks();

		$opponentsMove = "";
		if ($aiDrew > 0) {
			$opponentsMove = "Your opponent drew $aiDrew stick". ($aiDrew > 1 ? "s" : "");
		}
		//Make a visualisation of the sticks 
		$sticks = "";
		for ($i = 0; $i < $numSticks; $i++) {
			$sticks .= "I"; //Sticks remaining
		}
		for (; $i < $aiDrew + $numSticks; $i++) {
			$sticks .= "."; //Sticks taken by opponent
		}
		for (; $i < $startingNumberOfSticks; $i++) {
			$sticks .= "_"; //old sticks
		}
		return "<p>There is $numSticks stick" . ($numSticks > 1 ? "s" : "") ." left</p>
				<p style='font-family: \"Courier New\", Courier, monospace'>$sticks</p>
				<p>$opponentsMove</p>";
	}

	/** 
	* @return String HTML
	*/
	private function showSelection() {
		
		$numSticks = $this->game->getNumberOfSticks();
		$drawGET = self::$drawGET;

		$ret = "<h2>Select number of sticks</h2>
				<p>The player who draws the last stick looses</p>";
		$ret .= "<ol>";
		for ($i = 1; $i <= 3 && $i < $numSticks; $i++ ) {

			$ret .= "<li><a href='?$drawGET=$i'>Draw $i stick". ($i > 1 ? "s" : ""). "</a></li>";
		}
		$ret .= "<ol>";

		return $ret;
	}

	/** 
	* @return String HTML
	*/
	private function showWinner() {
		if ($this->playerWon) {
			return "<h2>Congratulations</h2>
					<p>You force the opponent to draw the last stick!</p>";
		} else {
			return "<h2>Epic FAIL!</h2>
					<p>You cant draw the last stick</p>";
		}
	}

	/** 
	* @return String HTML
	*/
	private function startOver() {
		$startOverGET = self::$startOverGET;
		return "<a href='?$startOverGET'>Start new game</a>";
		
	}
}