<?php

namespace form\view;

require_once("./src/form/view/FormView.php");

/**
 * @author Peter Emilsson
 * Class responsible for deleting forms and question
 */
class Delete extends \form\view\FormView {

	/**
	 * @return bool
	 */
	public function deleteQuestion() {
		return isset($_GET[$this->navigationView->getQuestion()]);
	}

	/**
	 * @return bool
	 */
	public function deleteForm() {
		return !isset($_GET[$this->navigationView->getQuestion()]) &&
				isset($_GET[$this->navigationView->getForm()]);
	}

	/**
	 * @return question id from url
	 * @throws \Exception If no id is found
	 */
	public function getQuestionId() {
		$idGET = $this->navigationView->getQuestion();
		if (empty($_GET[$idGET]))
			throw new \Exception('No question id in url');
		return $_GET[$idGET];
	}

	/**
	 * Formobserver implementation
	 */

	public function deleteOk($fId = null, $qId = null) {
		if ($fId !== null) {
			$this->saveMessage(1305);
			$this->navigationView->goToManageForm($fId);
		}
		else {
			$this->saveMessage(1211);
			$this->navigationView->goToHome();
		}
		exit();//Exit script
	}

	public function deleteFailed($fId = null, $qId = null) {
		$this->saveMessage(1304);
		$this->navigationView->goToManageForm($fId);
		exit();//Exit script
	}
}