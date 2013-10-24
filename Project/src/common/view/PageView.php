<?php

namespace common\view;

class PageView {

	private $metaTags = array();

	private $javaScriptTags = array();

	public function addStyleSheet($href, $media = "screen") {
		$this->metaTags[] = "<link href='$href' rel='stylesheet' media='$media'>";
	}

	public function addJavaScript($src) {
		$this->javaScriptTags[] = "<script src='$src'></script>";
	}

	public function getHTML(\common\view\Page $page) {
		$headCss = $this->getHeadTags();
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
    						<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    						<!--[if lt IE 9]>
      							<script src='javascript/vendor/html5shiv.js'></script>
      							<script src='javascript/vendor/respond.min.js'></script>
    						<![endif]-->
    						$headCss
    					</head>
    					<body>
    						$page->body
	    					<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	    					<script src='//code.jquery.com/jquery.js'></script>
	    					<script src='javascript/vendor/bootstrap.min.js'></script>
	    					$bodyJs
	    				</body>
	    			</html>";
	    return $html;
	}

	private function getHeadTags() {
		$metaTags = "";
		foreach ($this->metaTags as $tag) {
			$metaTags .= $tag . "\n";
		}
		return $metaTags;
	}

	private function getJavaScriptTags() {
		$jsTags = "";
		foreach ($this->javaScriptTags as $tag) {
			$jsTags .= $tag . "\n";
		}
		return $jsTags;
	}
}