<?php

namespace form\view;

require_once("./src/form/view/FormView.php");
require_once("./src/form/model/Form.php");

/**
 * @author Peter Emilsson
 * Class responsible for listing forms
 */
class ListForms extends \form\view\FormView {

	/**
	 * @param  array of \form\model\SubmittedFormCredentials $submittedFormsCredArray
	 * @return string HTML
	 */
	public function getSubmittedFormsHTML($submittedFormsCredArray) {
		$html = "";
		if (count($submittedFormsCredArray) > 0) {
			$html .= "
					<ul class='list-unstyled'>";
			foreach ($submittedFormsCredArray as $form) {
				$html .= $this->getSubmittedPanelHTML($form);
			}
			$html .= "</ul>";
		} else
			$html = "<h2>You have not submitted any forms</h2>";
		return $html;
	}

	/**
	 * @param  \form\model\FormCollection  $formCollection
	 * @param  boolean $manage      
	 * @return string HTML
	 */
	public function getHTML($formCollection, $manage = false) {
		$html = "";
		if ($manage) {
			$html = $this->getManageHTML($formCollection);
		} else {
			if ($formCollection->count() > 0) {
				$html = "
					<ul class='list-unstyled'>";

				foreach ($formCollection as $form) {
					$html .= $this->getPanelHTML($form, $manage);
				}

				$html .= "</ul>";
			} else
				$html = "<h2>No forms</h2>";
		}
		return $html;
	}

	/**
	 * @param  \form\model\FormCollection  $formCollection
	 * @return string HTML
	 */
	private function getManageHTML($formCollection) {
		$publishedAndActive = $formCollection->getPublishedAndActive();
		$ended = $formCollection->getEnded();
		$notPublished = $formCollection->getNotPublished();

		$html = "
			<ul class='list-unstyled'>
				<li><h3>Published and active</h3>
					<ul class='list-unstyled'>";

		$html .= $this->getFormsHTML($publishedAndActive);

		$html .= "
					</ul>
				</li>
				<li>
					<ul class='list-unstyled'>
						<li><h3>Not Published</h3>
							<ul class='list-unstyled'>";

		$html .= $this->getFormsHTML($notPublished);

		$html .= "		</li>
					</ul>
				</li>
				<li>
					<ul class='list-unstyled'>
						<li><h3>Finnished</h3>
							<ul class='list-unstyled'>";

		$html .= $this->getFormsHTML($ended);

		$html .= "			</ul>
						</li>
					</ul>
				</li>
			</ul>";

		return $html;
	} 

	/**
	 * @param  array of \form\model\Form $formArray
	 * @return string HTML
	 */
	private function getFormsHTML($formArray) {
		$html = "";
		if (count($formArray) > 0) {
			foreach ($formArray as $form) {
				$html .= $this->getPanelHTML($form, true);
			}
		} else {
			$html .= "<li><h4>None</h4></li>";
		}
		return $html;
	}

	/**
	 * @param  \form\model\Form $form
	 * @param  bool $manage 
	 * @return string HTML
	 */
	private function getPanelHTML(\form\model\Form $form, $manage) {
		$title = $form->getTitle();
		$description = $form->getDescription();
		$endDate = $form->getEndDate();
		$id = $form->getId();
		$createdDate = $form->getCreatedDate();
		$lastUpdateDate = $form->getLastUpdatedDate();
		$author = $form->getAuthor();
		$authorName = $author->getUsername();
		$link = $this->navigationView->getGoToFormLink($id);

		$html = "
			<li>
				<div class='panel panel-default'>
  					<div class='panel-heading'>
  						<h3 class='panel-title'>$title <small>by $authorName</small></h3>
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
    		$editLink = $this->navigationView->getGoToManageFormLink($id);
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

	/**
	 * @param  \form\model\SubmittedFormCredentials $submittedFormCred
	 * @return string HTML
	 */
	private function getSubmittedPanelHTML(\form\model\SubmittedFormCredentials $submittedFormCred) {
		$title = $submittedFormCred->getTitle();
		$description = $submittedFormCred->getDescription();
		$endDate = $submittedFormCred->getEndDate();
		$formId = $submittedFormCred->getFormId();
		$submitted = $submittedFormCred->getSubmittedDate();
		$lastUpdateDate = $submittedFormCred->getLastUpdatedDate();
		$author = $submittedFormCred->getAuthor();
		$authorName = $author->getUsername();
		$userFormId = $submittedFormCred->getUserFormId();
		$link = $this->navigationView->getShowSubmittedFormLink($formId, $userFormId);
		$html = "
			<li>
				<div class='panel panel-default'>
  					<div class='panel-heading'>
  						<h3 class='panel-title'>$title <small>by $authorName</small></h3>
  					</div>
 					<div class='panel-body'>
    					$description
    					<br/>
    					<ul class='list-inline'>
    						<li>Submitted: $submitted</li>
    						<li>Last Updated: $lastUpdateDate</li>";

    	if ($endDate->hasPassed()) {
    		$html .= "		<li>Ended: Yes</li>";
    	} else {
    		$html .= "		<li>Ended: No</li>";
    	}

    	$html .= "			<li>End Date: $endDate</li>
    					</ul>
    					<span class='pull-right'>
    						<a href='$link'>Open</a>
  					</div>
				</div>
			</li>";
		return $html;
	} 

	/**
	 * Formobserver implementation
	 */

	public function getFailed($fId = null, $qId = null) {
		$this->saveMessage(1204);
		$this->navigationView->goToHome();
		exit();//Exit script
	}
}