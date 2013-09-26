<?php

session_start();

require_once('LikeController.php');
require_once('PageView.php');


class MasterController {
	public static function doControll() {
		// @error, old comment
		//TODO: Show that this is a controller
		$likeController = new LikeController();
		
		$html = $likeController->doControll();
		
		// @error, old comment
		//TODO: Use Common/PageView
		$pageView = new \Common\PageView();
		
		
		return $pageView->GetHTMLPage("I like titles", $html);
	}
	
}

echo MasterController::doControll();


 
