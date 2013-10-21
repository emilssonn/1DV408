<?php


namespace common\view;

/**
 * PageView generates HTML pages from title and body-texts
 * also handles charsets and stylesheets
 **/
class PageView {
  
  private $metaTags = array();
  private $charset;
  
  /**
  * Called when using new PageView();
  * @param string $charset  
  **/
  public function __construct($charset = "utf-8") {
    $this->charset = $charset;
  }
  
  /**
  * Adds a CSS stylesheet to the head of the document
  * @param urlstring $href url to css file
  **/
  public function AddStyleSheet($href) {
    //TODO: "/>" is not really valid for "HTML 4.01 Transitional" so these tags are not ended
    $this->metaTags[] = "<link rel='StyleSheet' href='$href' type='text/css'";
  }
  
  /**
   * Builds meta and CSS tags as a HTML/XML string
   *    
   * @param bool $isXML is the document an XML file and tags should be closed 
   * @return string  
   */
  private function BuildHeadTags($isXML) {
    $end = ">";
    if ($isXML) {
      $end = "/>";
    }
    $retValue = "";
    foreach($this->metaTags as $tag) {
      $retValue .= $tag . "$end\n            "; // "\n            " for readability
    }
    return $retValue;
  }
  
  /**
  * Returns a HTML 4.01 Transitional page
  * @param string $title  
  * @param string $body    
  * @return string     
  **/
  public function GetHTMLPage(\common\view\Page $page) {
    
    $head = $this->BuildHeadTags(false);
    
    $html = "
    <!DOCTYPE HTML SYSTEM>
    <html>
    <head>
      <title>$page->title</title>
      <meta http-equiv='content-type' content='text/html; charset=$this->charset'>
      $head
    </head>
    <body>
      $page->body
    </body>
    </html>";
    
    return $html;
  }
}
