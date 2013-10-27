<?php

namespace form\view;

require_once("./src/form/model/FormObserver.php");
require_once("./src/form/model/FormCredentials.php");

class ListForms implements \form\model\FormObserver {

	private $navigationView;

	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
	}

	public function getHTML($forms, $manage = false) {
		$html = "
			<ul class='list-unstyled'>
				<li>Published and active
					<ul class='list-unstyled'>";

		foreach ($forms as $form) {
			$title = $form->getTitle();
			$description = $form->getDescription();
			$endDate = $form->getEndDate();
			$id = $form->getId();
			$createdDate = $form->getCreatedDate();
			$lastUpdateDate = $form->getLastUpdatedDate();
			$authorId = $form->getAuthorId();
			$link = $this->navigationView->getGoToFormLink($id);

			if (!$form->isPublished()) {
				$html .= "
					</ul>
				</li>
				<li>
					<ul class='list-unstyled'>
						<li>Not Published
							<ul class='list-unstyled'>";
			}
			$html .= $this->getPanelHTML($title, $description, $endDate, $id, 
				$createdDate, $lastUpdateDate, $authorId, $link, $manage);
		}

		$html .= "</ul></li></ul>";
		return $html;
	}

	private function getPanelHTML(	$title, $description, $endDate, $id, $createdDate, 
									$lastUpdateDate, $authorId, $link, $manage) {
		$html = "
			<li>
				<div class='panel panel-default'>
  					<div class='panel-heading'>
  						<h3 class='panel-title'>$title <small>by $authorId</small></h3>
  					</div>
 					<div class='panel-body'>
    					$description
    					<br/>
    					<ul class='list-inline'>
    						<li>End Date: $endDate</li>
    						<li>Created: $createdDate</li>
    						<li>Last Updated: $lastUpdateDate</li>
    					</ul>
    					<span class='pull-right'>
    						<a href='$link'>Open</a>";

    	if ($manage) {
    		$editLink = $this->navigationView->getGoToEditFormLink($id);
    		$resultLink = $this->navigationView->getFormResultLink($id);
    		$html .= "		<a href='$editLink'>Manage</a>
    						<a href='$resultLink'>Results</a>";
    	}

    	$html .= "
  					</div>
				</div>
			</li>";

		return $html;
	}



	public function addFormOk(\form\model\FormCredentials $formCred) {

	}

	public function addFormFailed() {

	}

	public function getFormOk() {

	}

	public function getFormFailed() {

	}
}