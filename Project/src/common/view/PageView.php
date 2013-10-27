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
    						<div class='navbar navbar-fixed-top navbar-default' role='navigation'>

						        <div class='navbar-header'>

						          <button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.navbar-collapse'>
						            <span class='icon-bar'></span>
						            <span class='icon-bar'></span>
						            <span class='icon-bar'></span>
						          </button>

						          <a class='navbar-brand' href='#'>Project name</a>
						          <button type='button' class='navbar-toggle' id='menu-toggle'>
						            <span class='icon-bar'></span>
						            <span class='icon-bar'></span>
						            <span class='icon-bar'></span>
						          </button>

						          
						        </div>

						        <div class='collapse navbar-collapse'>

						          <ul class='nav navbar-nav navbar-right'>
						            <li class='active'><a href='#'>Home</a></li>
						            <li><a href='#about'>About</a></li>
						            <li><a href='#contact'>Contact</a></li>
						          </ul>
						        </div><!-- /.nav-collapse -->
						    </div><!-- /.navbar -->

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