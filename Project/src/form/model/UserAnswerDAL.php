<?php

namespace form\model;

require_once("./src/common/model/DbConnection.php");
require_once("./src/form/model/QuestionResultCredentials.php");
require_once("./src/form/model/AnswerResultCredentials.php");

class UserAnswerDAL {

	private static $userAnswerTable = "user_answer";

	private $dbConnection;

	private $user;

	public function __construct(\authorization\model\UserCredentials $user = null) {
		$this->dbConnection = \common\model\DbConnection::getInstance();
		$this->user = $user;
	}

	public function getFormResult(\form\model\Form $form) {
		$templateAnswerDAL = new \form\model\TemplateAnswerDAL();
		$questionResultsArray = array();
		$userAnswerTable = self::$userAnswerTable;
		$templateAnswerTable = $templateAnswerDAL->getTemplateAnswerTable();

		foreach ($form->getQuestions() as $question) {
			$sql = "SELECT 
				$userAnswerTable.answer_id,
				COUNT(1) as amount,
				$templateAnswerTable.title
				FROM $userAnswerTable
				INNER JOIN $templateAnswerTable
					ON $userAnswerTable.answer_id = $templateAnswerTable.id 
				WHERE $userAnswerTable.question_id = ? 
				GROUP BY $userAnswerTable.answer_id";
			
			$qId = $question->getId();
			$qTitle = $question->getTitle();
			$qDescription = $question->getDescription();

			$statement = $this->dbConnection->runSql($sql, 
					array($qId),
					"i");

			$result = $statement->get_result();
            
            $questionResultCred = new \form\model\QuestionResultCredentials($qId, $qTitle, $qDescription);
	        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
	        	$questionResultCred->addAnswerResult(
	        		new \form\model\AnswerResultCredentials(
	        			$object['answer_id'], $object['amount'], $object['title']));
	        }
	        $questionResultsArray[] = $questionResultCred;
		}

		return $questionResultsArray;
	}

	public function insertUserAnswers($answerViewCredentialsArray, $id) {
		$sql = "INSERT INTO " . self::$userAnswerTable . "
				(
					user_form_id,
					question_id,
					answer_id
				)
				VALUES(?, ?, ?)";

		foreach ($answerViewCredentialsArray as $aCred) {
			$statement = $this->dbConnection->runSql($sql, 
			array($id, $aCred->getQuestionId(), $aCred->getAnswerId()),
			"iii");
		}
	}

	public function getUserFormResult($userFormId) {
		$userAnswerTable = self::$userAnswerTable;
		$ret = array();
		$sql = "SELECT 
				id,
				question_id,
				answer_id,
				note_text
				FROM $userAnswerTable 
				WHERE user_form_id = ? 
				ORDER BY question_id ASC";

		$statement = $this->dbConnection->runSql($sql, 
				array($userFormId),
				"i");
            
        $result = $statement->get_result();

        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
                $ret[] = \form\model\AnswerViewCredentials::createFull(
                	$object["id"], 
                	$object["question_id"], 
                	$object["answer_id"], 
                	$object["note_text"]);
        }
		$statement->free_result();

		return $ret;
	}

	public function updateUserAnswers($submittedFormCredentials, $answers) {
		$answerResults = $submittedFormCredentials->getAnswersResult();
		$updateAnswers = array();
		$insertAnswers = array();
		foreach ($answers as $answer) {
			$removeIndex = null;
			foreach ($answerResults as $key => $value) {
				if ($answer->getQuestionId() == $value->getQuestionId()) {
					$answer->setId($value->getId());
					$updateAnswers[] = $answer;
					$removeIndex = $key;
					break;
				}
			}
			if (is_numeric($removeIndex)) {
				unset($answerResults[$removeIndex]);
				$answerResults = array_values($answerResults);
			} else {
				$insertAnswers[] = $answer;
			}
		}
		if (count($updateAnswers) > 0)
			$this->updateOldAnswers($updateAnswers);

		if (count($insertAnswers) > 0)
			$this->insertUserAnswers($insertAnswers, $submittedFormCredentials->getUserFormId());
	}

	private function updateOldAnswers($updateAnswers) {
		$userAnswerTable = self::$userAnswerTable;
		$sql = "UPDATE $userAnswerTable 
				SET answer_id = CASE id ";
		$ids = "";
		//Build sql to only have one database query
		foreach ($updateAnswers as $key => $value) {
			$sql .= sprintf("WHEN %d THEN %d ", $value->getId(), $value->getAnswerId());
			if ($key == 0) {
				$ids .= $value->getId();
			} else {
				$ids .= ", " . $value->getId();
			}
		}
		$sql .= "END WHERE id IN ($ids)";
		$statement = $this->dbConnection->runSql($sql);
	}
}