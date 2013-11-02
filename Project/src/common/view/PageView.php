<?php

namespace common\view;

/**
 * @author Daniel Toll - https://github.com/dntoll
 * Changes by Peter Emilsson
 * Combines HTML for output
 */
class PageView {

	/**
	 * @var array of HTML css tags
	 */
	private $cssTags = array();

	/**
	 * @var array of HTML javascript tags
	 */
	private $javaScriptTags = array();

	/**
	 * @param string $href
	 * @param string $media
	 */
	public function addStyleSheet($href, $media = "screen") {
		$this->cssTags[] = "<link href='$href' rel='stylesheet' media='$media'>";
	}

	/**
	 * @param string $src
	 */
	public function addJavaScript($src) {
		$this->javaScriptTags[] = "<script src='$src'></script>";
	}

	/**
	 * @param  \common\view\Page $page
	 * @return String HTML           
	 */
	public function getHTML(\common\view\Page $page) {
		$headCss = $this->getCssTags();
		$bodyJs = $this->getJavaScriptTags();

		$html =
				"<!DOCTYPE html>
					<html>
						<head>
							<title>$page->title</title>
							<meta charset='UTF-8'>
							<meta name='viewport' content='width=device-width, initial-scale=1.0'>
							<!-- Bootstrap -->
    						<link href='css/vendor/bootstrap.min.css' rel='stylesheet' media='screen'>		
    						<link href='css/custom.css' rel='stylesheet' media='screen'>		
    						<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    						<!--[if lt IE 9]>
      							<script src='javascript/vendor/html5shiv.js'></script>
      							<script src='javascript/vendor/respond.min.js'></script>
    						<![endif]-->
    						$headCss
    					</head>
    					<body>
    						$page->navBar

						    <div id='wrapper'>
								$page->menu
								
								<!-- Page content -->
      							<div id='page-content-wrapper'>
        							
        							<!-- Keep all page content within the page-content inset div! -->
       								<div class='page-content inset'>
          								<div class='row'>
          									$page->body
           								</div>
        							</div>
     							</div>
     						</div>

	    					<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	    					<script src='javascript/vendor/jquery-1.10.2.min.js'></script>
	    					<script src='javascript/vendor/bootstrap.min.js'></script>
	    					$bodyJs
	    				</body>
	    			</html>";
	    return $html;
	}

	/**
	 * @return String HTML of all css tags
	 */
	private function getCssTags() {
		$cssTags = "";
		foreach ($this->cssTags as $tag) {
			$cssTags .= $tag . "\n";
		}
		return $cssTags;
	}

	/**
	 * @return string HTML of all javascript tags
	 */
	private function getJavaScriptTags() {
		$jsTags = "";
		foreach ($this->javaScriptTags as $tag) {
			$jsTags .= $tag . "\n";
		}
		return $jsTags;
	}
}