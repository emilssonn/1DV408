<?php

namespace form\view;

require_once("./src/form/model/FormObserver.php");

class ViewResults implements \form\model\FormObserver {

	private $colors = array("#F38630", "#4D5360", "#69D2E7", "#F7464A");

	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
	}

	public function getHTML(\form\model\Form $form, $formResultsArray) {
		$html = $this->getHeadHTML($form);
		$html .= $this->getResultHTML($formResultsArray);
		return $html;
	}

	private function getHeadHTML(\form\model\Form $form) {
		$formCred = $form->getFormCredentials();
		$title = $formCred->getTitle();
		$description = $formCred->getDescription();
		$endDate = $formCred->getEndDate();
		$id = $formCred->getId();
		$html = "
			<div>
				<h1>$title</h1>
				<p class='lead'>$description</p>";

		if (strtotime($endDate) < time()) {
			$html .= "<h4>Ended: Yes</h4>
					<p>Date: $endDate</p>";
		} else {
			$html .= "<h4>Ended: No</h4>
					<p>Ends: $endDate</p>";
		}

		return $html;
	}

	private function getResultHTML($formResultsArray) {
		$html = "";
		foreach ($formResultsArray as $key => $questionResult) {
			$qTitle = $questionResult->getTitle();
			$qDescription = $questionResult->getQuestionDescription();
			$key += 1;
			$html .= "
				<div class='qResult row'>
					<div class='col-lg-4'>
						<h3>$key: $qTitle</h3>
						<p>$qDescription</p>
						<ul class='list-unstyled'>";

			$answerResultsArray = $questionResult->getAnswersResult();
			foreach ($answerResultsArray as $key => $answerResult) {
				$aText = $answerResult->getText();
				$aAmount = $answerResult->getAmount();
				$color = $this->colors[$key];
				$html .= "
						<li>
							<label class='label label-default' style='background-color: $color;'>
							$aText: <span data-color='$color'>$aAmount</span></label>
						</li>";
			}
			$html .= "
					</ul>
					</div>
					<div class='col-lg-8'>
						<canvas height='250' width='250'></canvas>
					</div>
				</div>
				<hr/>";
		}
		return $html;
	}

	public function getFormId() {
		$idGET = $this->navigationView->getForm();
		if (empty($_GET[$idGET]))
			throw new \Exception('No form id in url');
		return $_GET[$idGET];
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