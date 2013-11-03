<?php

namespace form\model;

require_once("./src/common/model/DbConnection.php");
require_once("./src/form/model/QuestionResultCredentials.php");
require_once("./src/form/model/AnswerResultCredentials.php");

/**
 * @author Peter Emilsson
 * Class used to access the table "user_answer"
 * All functions throws \Exception on error
 */
class UserAnswerDAL {

	/**
	 * @var string
	 */
	private static $userAnswerTable = "user_answer";

	/**
	 * @var \common\model\DbConnection
	 */
	private $dbConnection;

	/**
	 * @var \user\model\UserCredentials
	 */
	private $user;

	/**
	 * @param \user\model\UserCredentials $user, logged in user
	 */
	public function __construct(\user\model\UserCredentials $user = null) {
		$this->dbConnection = \common\model\DbConnection::getInstance();
		$this->user = $user;
	}

	/**
	 * @param  \form\model\Form $form
	 * @return array of \form\model\QuestionResultCredentials
	 * @throws \Exception If database query fails
	 */
	public function getFormResult(\form\model\Form $form) {
		$templateAnswerDAL = new \form\model\TemplateAnswerDAL();
		$questionResultsArray = array();
		$userAnswerTable = self::$userAnswerTable;
		$templateAnswerTable = $templateAnswerDAL->getTemplateAnswerTable();

		foreach ($form->getQuestions() as $question) {
			$sql = "SELECT 
				$templateAnswerTable.id,
				COUNT($userAnswerTable.answer_id) as amount,
				$templateAnswerTable.title
				FROM $userAnswerTable
				RIGHT OUTER JOIN $templateAnswerTable
					ON  $userAnswerTable.answer_id = $templateAnswerTable.id
				WHERE $templateAnswerTable.question_id = ? 
				GROUP BY $templateAnswerTable.id";
			
			$qId = $question->getId();
			$qTitle = $question->getTitle();
			$qDescription = $question->getDescription();

			$stmt = $this->dbConnection->runSql($sql, 
					array($qId),
					"i");

			$result = $stmt->get_result();
            
            $questionResultCred = new \form\model\QuestionResultCredentials($qId, $qTitle, $qDescription);
	        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
	        	$questionResultCred->addAnswerResult(
	        		new \form\model\AnswerResultCredentials(
	        			$object['id'], $object['amount'], $object['title']));
	        }
	        $questionResultsArray[] = $questionResultCred;
	        $stmt->free_result();
		}
		
		return $questionResultsArray;
	}

	/**
	 * @param  array of \form\model\AnswerViewCredentials $answerViewCredentialsArray
	 * @param  int $userFormId
	 * @throws \Exception If database query fails
	 */
	public function insertUserAnswers($answerViewCredentialsArray, $userFormId) {
		$sql = "INSERT INTO " . self::$userAnswerTable . "
				(
					user_form_id,
					question_id,
					answer_id,
					note_text,
					comment_text
				)
				VALUES(?, ?, ?, ?, ?)";

		foreach ($answerViewCredentialsArray as $aCred) {
			$answer = $aCred->getAnswer();
			$this->dbConnection->runSql($sql, 
				array($userFormId, $aCred->getQuestionId(), $answer->getAnswerId(), 
					$answer->getNoteText(), $aCred->getCommentText()),
				"iiiss");
		}
	}

	/**
	 * @param  int $userFormId
	 * @return array of \form\model\QuestionViewCredentials
	 * @throws \Exception If database query fails
	 */
	public function getUserFormResult($userFormId) {
		$userAnswerTable = self::$userAnswerTable;
		$ret = array();
		$sql = "SELECT 
				id,
				question_id,
				answer_id,
				note_text,
				comment_text
				FROM $userAnswerTable 
				WHERE user_form_id = ? 
				ORDER BY question_id ASC";

		$stmt = $this->dbConnection->runSql($sql, 
				array($userFormId),
				"i");
            
        $result = $stmt->get_result();

        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
        		$answerViewCred = new \form\model\AnswerViewCredentials($object["answer_id"], $object["note_text"]);
        		$ret[] = \form\model\QuestionViewCredentials::createFull(
        			$object["id"], 
        			$object["question_id"], 
        			$object["comment_text"], 
        			$answerViewCred);
        }
		
		$stmt->free_result();
		return $ret;
	}

	/**
	 * @param  \form\model\SubmittedFormCredentials $submittedFormCredentials
	 * @param  array \form\model\QuestionViewCredentials $answers                       
	 * @throws \Exception If database query fails                  
	 */
	public function updateUserAnswers(\form\model\SubmittedFormCredentials $submittedFormCredentials, 
										$answers) {
		$questionViewCredentials = $submittedFormCredentials->getAnswersResult();
		$updateAnswers = array();
		$insertAnswers = array();
		foreach ($answers as $answer) {
			$removeIndex = null;
			foreach ($questionViewCredentials as $key => $value) {
				if ($answer->getQuestionId() == $value->getQuestionId()) {
					$answer->setId($value->getId());
					$updateAnswers[] = $answer;
					$removeIndex = $key;
					break;
				}
			}
			if (is_numeric($removeIndex)) {
				unset($questionViewCredentials[$removeIndex]);
				$questionViewCredentials = array_values($questionViewCredentials);
			} else {
				$insertAnswers[] = $answer;
			}
		}
		if (count($updateAnswers) > 0)
			$this->updateOldAnswers($updateAnswers);

		if (count($insertAnswers) > 0)
			$this->insertUserAnswers($insertAnswers, $submittedFormCredentials->getUserFormId());
	}

	/**
	 * @param  array of \form\model\QuestionViewCredentials $updateAnswers 
	 * @throws \Exception If database query fails        
	 */
	private function updateOldAnswers($updateAnswers) {
		$userAnswerTable = self::$userAnswerTable;
		$statement;
		$sql = "UPDATE $userAnswerTable
				SET answer_id = ?,
					note_text = ?,
					comment_text = ?
				WHERE id = ?";

		foreach ($updateAnswers as $key => $value) {
			$answer = $value->getAnswer();
			$this->dbConnection->runSql($sql, 
			array($answer->getAnswerId(), $answer->getNoteText(),  $value->getCommentText(), $value->getId()),
			"issi");
		}
	}
}