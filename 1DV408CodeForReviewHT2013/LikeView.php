<?php

class LikeView {
	// @error, consts are global
	// @error, comment, no type
	const USER_DID_LIKE = 0;
	const NO_MESSAGE = 1;
	
	/**
	 * String location in $_GET
	 */
	// @error, bad name
	private $m_getLikeLocation = "ILike";
	
	
	/**
	 * Collect input 
	 * @return boolean 
	 */
	public function didUserLike() {
		return isset($_GET[$this->m_getLikeLocation]);
	}
	
	// @error, $likes should have "int" comment
	/**
	 * Generate HTML output 
	 * @param $likes, Number of likes
	 * @param $userHasLiked, boolean
	 * @param $message, CONST  NO_MESSAGE | USER_DID_LIKE
	 * @return String,  HTML
	 */
	// @error, name getOutput
	public function doOutput($likes, $userHasLiked, $message) {
		
		if ($userHasLiked == false) {
			$likeButton = "<a href='?$this->m_getLikeLocation'>I Like!</a>";
		} else {
			$likeButton = "You like...";
		}
		$messageHTML ="";
		if ($message == self::NO_MESSAGE) {
			$messageHTML = "No event";
		} else {
			$messageHTML = "<h3>You pressed like, thank you! </h3>";
		}
		// @error, language
		return "
				<div>
					<h1>LikeView</h1>
					Antalet likes är $likes
					
					$likeButton
					
					<br/>
					
					$messageHTML
				</div>";
	}
	
	
}