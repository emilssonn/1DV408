<?php

namespace form\view;

require_once("./src/form/model/FormObserver.php");

class ViewResults implements \form\model\FormObserver {

	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
	}

	public function getHTML(\form\model\Form $form, $formResultsArray) {
		$html = $this->getFormHeadHTML($form);
		$html .= $this->getResultHTML($formResultsArray);
		return $html;
	}

	private function getFormHeadHTML(\form\model\Form $form) {
		$formCred = $form->getFormCredentials();
		$title = $formCred->getTitle();
		$description = $formCred->getDescription();
		$endDate = $formCred->getEndDate();
		$id = $formCred->getId();
		$html = "
				<h3>$title</h3>
				<p>$description</p>
				<h4>End date</h4>
					$endDate
				";

		return $html;
	}

	private function getResultHTML($formResultsArray) {
		$html = "";
		foreach ($formResultsArray as $questionResult) {
			$qTitle = $questionResult->getTitle();
			$qDescription = $questionResult->getQuestionDescription();
			$html .= "
				<h4>$qTitle</h4>
				<p>$qDescription</p>";

			$answerResultsArray = $questionResult->getAnswersResult();
			foreach ($answerResultsArray as $answerResult) {
				$aText = $answerResult->getText();
				$aAmount = $answerResult->getAmount();
				$html .= "
					<h5>$aText</h5>
					<p>Antal: $aAmount</p>";
			}
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