<?php

namespace form\model;

require_once("./src/common/model/DbConnection.php");
require_once("./src/form/model/TemplateAnswerDAL.php");

class TemplateQuestionDAL {

	private static $templateQuestionTable = "template_question";

	private $dbConnection;

	private $user;

	private $templateAnswerDAL;

	public function __construct(\authorization\model\UserCredentials $user = null) {
		$this->dbConnection = \common\model\DbConnection::getInstance();
		$this->user = $user;
		$this->templateAnswerDAL = new \form\model\TemplateAnswerDAL();
	}

	public function getQuestionsByFormId($formId) {
		$questions = array();
		$sql = 'SELECT
				id, 
				title, 
				description
				FROM ' . self::$templateQuestionTable . '
				 WHERE form_id = ?';
		
		$statement = $this->dbConnection->runSql($sql, array($formId), "i");

		$result = $statement->get_result();

        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
                $questions[] = \form\model\QuestionCredentials::createFull(
                	$object["title"], 
                	$object["description"], 
                	$object["id"]);
        }

        foreach ($questions as $question) {
        	$answers = $this->templateAnswerDAL->getAnswersByQuestion($question);
        	$question->addAnswers($answers);
        }

        return $questions;
	}

	/**
	 * [insertQuestion description]
	 * @param  formmodelQuestionCredentials $questionCred [description]
	 * @param  [type]                       $formId       [description]
	 * @return [type]                                     [description]
	 */
	public function insertQuestion(\form\model\QuestionCredentials $questionCred, $formId) {
		$sql = "INSERT INTO " . self::$templateQuestionTable . "
				(
					title,
					description,
					form_id
				)
				VALUES(?, ?, ?)";

		$statement = $this->dbConnection->runSql($sql, 
			array($questionCred->getTitle(), $questionCred->getDescription(), $formId), 
			"ssi");

		$questionId = $this->dbConnection->getLastInsertedId();
		$this->templateAnswerDAL->insertAnswers($questionCred->getAnswers(), $questionId);
	}

	public function getQuestionById($qId) {
		$questionCred;
		$sql = 'SELECT
				id, 
				title, 
				description
				FROM ' . self::$templateQuestionTable . '
				 WHERE id = ?';
		
		$statement = $this->dbConnection->runSql($sql, array($qId), "i");

		$result = $statement->bind_result($id, $title, $description);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}

		if ($statement->fetch()) {
			$questionCred = \form\model\QuestionCredentials::createFull($title, $description, $id);
		} else {
			throw new \Exception("Question not found in database");
		}

		$statement->free_result();
        $answers = $this->templateAnswerDAL->getAnswersByQuestion($questionCred);
        $questionCred->addAnswers($answers);

        return $questionCred;
	}

	/**
	 * [updateQuestion description]
	 * @param  formmodelQuestionCredentials $questionCred [description]
	 * @param  [type]                       $formId       [description]
	 * @return [type]                                     [description]
	 */
	public function updateQuestion(\form\model\QuestionCredentials $questionCred, $formId) {
		$sql = "UPDATE " . self::$templateQuestionTable . "
				SET 
					title = ?,
					description = ?
				WHERE id = ? AND form_id = ?";

		$statement = $this->dbConnection->runSql($sql, 
			array($questionCred->getTitle(), $questionCred->getDescription(), $questionCred->getId(), $formId), 
			"ssii");

		$this->templateAnswerDAL->updateAnswers($questionCred->getAnswers(), $questionCred->getId());
	}

}