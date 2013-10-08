<?php

namespace controller;

require_once("model/LastStickGame.php");
require_once("view/GameView.php");

// 3 kind of errors

class PlayGame {

	/**
	 * @var \model\LastStickGame
	 */
	private $game;

	/**
	 * @var \view\GameView
	 */
	private $view;

	//@error, string in controller
	/**
	 * @var string
	 */
	private $message = "";


	public function __construct() {
		$this->game = new \model\LastStickGame();
		$this->view = new \view\GameView($this->game);
	}

	/**
	* @return String HTML
	*/
	public function runGame() {
		//Handle input
		if ($this->game->isGameOver()) {
			$this->doGameOver();
		} else {
			$this->playGame();
		}

		//Generate Output
		return $this->view->show($this->message);
	}

	/**
	* Called when game is still running
	*/
	private function playGame() {
		if ($this->playerSelectSticks()) {
			try {
				$sticksDrawnByPlayer = $this->getNumberOfSticks();

				$this->game->playerSelectsSticks($sticksDrawnByPlayer, $this->view);
			} catch(\Exception $e) {
				//@error, html in controller
				$this->message = "<h1>Unauthorized input</h1>";
			}
		}
	}

	private function doGameOver() {
		if ($this->playerStartsOver()) {
			$this->game->newGame();
		}		
	}

	/** 
	* @return boolean
	*/
	private function playerSelectSticks() {
		//@error, GET in controller
		//@error, string dependancy to Gameview
		return isset($_GET["draw"]);
	}

	/** 
	* @return boolean
	*/
	private function playerStartsOver() {
		//@error, GET in controller
		//@error, string dependancy to Gameview
		return isset($_GET["startOver"]);
	}

	/** 
	* @return \model\StickSelection
	*/
	private function getNumberOfSticks() {
		//@error, GET in controller
		//@error, string dependancy to Gameview
		switch ($_GET["draw"]) {
			case 1 : return \model\StickSelection::One(); break;
			case 2 : return \model\StickSelection::Two(); break;
			case 3 : return \model\StickSelection::Three(); break;
		}
		throw new \Exception("Invalid input");
	}
}