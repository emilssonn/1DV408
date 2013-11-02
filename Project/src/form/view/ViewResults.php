<?php

namespace form\view;

require_once("./src/form/view/FormView.php");

/**
 * @author Peter Emilsson
 * Responsible for displaying compined results for a form
 */
class ViewResults extends \form\view\FormView {

	/**
	 * Colors for the charts, max number of diffrent answers per question = length of array or critical error
	 * @var array
	 */
	private $colors = array("#F38630", "#4D5360", "#69D2E7", "#F7464A");

	/**
	 * @param  \form\model\Form $form
	 * @param  array of \form\model\QuestionResultCredentials $qResultCredArray 
	 * @return string HTML
	 */
	public function getHTML(\form\model\Form $form, $qResultCredArray) {
		$html = $this->getHeadHTML($form);
		$html .= $this->getResultHTML($qResultCredArray);
		return $html;
	}

	/**
	 * @param  \form\model\Form $form
	 * @return string HTML
	 */
	private function getHeadHTML(\form\model\Form $form) {
		$title = $form->getTitle();
		$description = $form->getDescription();
		$endDate = $form->getEndDate();
		$id = $form->getId();
		$html = "
			<div>
				<h1>$title</h1>
				<p class='lead'>$description</p>";

		if ($endDate->hasPassed()) {
			$html .= "<h4>Ended: Yes</h4>
					<p>Date: $endDate</p>";
		} else {
			$html .= "<h4>Ended: No</h4>
					<p>Ends: $endDate</p>";
		}
		return $html;
	}

	/**
	 * @param  array of \form\model\QuestionResultCredentials $qResultCredArray 
	 * @return string HTML
	 */
	private function getResultHTML($qResultCredArray) {
		$html = "";
		foreach ($qResultCredArray as $key => $qResultCred) {
			$qTitle = $qResultCred->getTitle();
			$qDescription = $qResultCred->getQuestionDescription();
			$key += 1;
			$html .= "
				<div class='qResult row'>
					<div class='col-lg-4'>
						<h3>$key: $qTitle</h3>
						<p>$qDescription</p>
						<ul class='list-unstyled'>";

			$aResultCredArray = $qResultCred->getAnswersResult();
			foreach ($aResultCredArray as $key => $aResultCred) {
				$aText = $aResultCred->getText();
				$aAmount = $aResultCred->getAmount();
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
}