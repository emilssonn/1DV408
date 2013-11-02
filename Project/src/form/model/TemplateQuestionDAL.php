<?php

namespace form\model;

require_once("./src/common/model/DbConnection.php");
require_once("./src/form/model/TemplateAnswerDAL.php");

/**
 * @author Peter Emilsson
 * Responisble for the main actions to the template_question table
 * All functions with database querys will throw \Exception on error
 */
class TemplateQuestionDAL {

	/**
	 * Table name
	 * @var string
	 */
	private static $templateQuestionTable = "template_question";

	/**
	 * @var \common\model\DbConnection
	 */
	private $dbConnection;

	/**
	 * @var \user\model\UserCredentials
	 */
	private $user;

	/**
	 * @var \form\model\TemplateAnswerDAL
	 */
	private $templateAnswerDAL;

	/**
	 * @param \user\model\UserCredentials $user
	 */
	public function __construct(\user\model\UserCredentials $user = null) {
		$this->dbConnection = \common\model\DbConnection::getInstance();
		$this->user = $user;
		$this->templateAnswerDAL = new \form\model\TemplateAnswerDAL();
	}

	/**
	 * Make access public for other DAL classes to use
	 * Ex in JOIN statements
	 * @return string
	 */
	public function getTemplateQuestionTable() {
		return self::$templateQuestionTable;
	}

	/**
	 * @param  int $fId 
	 * @param  int $qId 
	 * @return bool  
	 * @throws \Exception If database query fails or question do not belong to form
	 */
	public function questionBelongsToForm($fId, $qId) {
		$sql = 'SELECT
				id
				FROM ' . self::$templateQuestionTable . '
				 WHERE form_id = ?
				 AND id = ?';

		$statement = $this->dbConnection->runSql($sql, array($fId, $qId), "ii");

		$result = $statement->bind_result($id);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}

		if ($statement->fetch()) {
			$statement->free_result();
			return true;
		} else {
			throw new \Exception("Question do not belong to form");
		}
	}

	/**
	 * @param  int $qId 
	 * @throws \Exception If database query fails
	 */
	public function deleteQuestion($qId) {
		$sql = "DELETE
				FROM " . self::$templateQuestionTable . " 
				WHERE id = ?";

		$this->dbConnection->runSql($sql, array($qId), "i");
	}

	/**
	 * @param  int $formId
	 * @return array of \form\model\QuestionCredentials
	 * @throws \Exception If database query fails
	 */
	public function getQuestionsByFormId($formId) {
		$questions = array();
		$sql = 'SELECT
				id, 
				title, 
				description,
				comment_text,
				required
				FROM ' . self::$templateQuestionTable . '
				 WHERE form_id = ?';
		
		$statement = $this->dbConnection->runSql($sql, array($formId), "i");

		$result = $statement->get_result();

        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
        	$required = (bool)$object["required"];
			$commentText = (bool)$object["comment_text"];
            $questions[] = \form\model\QuestionCredentials::createFull(
           		$object["title"], 
                $object["description"], 
                $object["id"],
                $required,
                $commentText);
        }

        foreach ($questions as $question) {
        	$answers = $this->templateAnswerDAL->getAnswersByQuestion($question);
        	$question->addAnswers($answers);
        }

        $statement->free_result();
        return $questions;
	}

	/**
	 * @param  \form\model\QuestionCredentials  $questionCred 
	 * @param  int                      		$formId     
	 * @throws \Exception If database query fails
	 */
	public function insertQuestion(\form\model\QuestionCredentials $questionCred, $formId) {
		$sql = "INSERT INTO " . self::$templateQuestionTable . "
				(
					title,
					description,
					comment_text,
					required,
					form_id
				)
				VALUES(?, ?, ?, ? ,?)";

		$statement = $this->dbConnection->runSql($sql, 
			array($questionCred->getTitle(), $questionCred->getDescription(), $questionCred->commentText(), $questionCred->isRequired(), $formId), 
			"ssiii");

		$questionId = $this->dbConnection->getLastInsertedId();
		$statement->free_result();
		$this->templateAnswerDAL->insertAnswers($questionCred->getAnswers(), $questionId);
	}

	/**
	 * @param  int $qId 
	 * @return \form\model\QuestionCredentials
	 * @throws \Exception If database query fails or question not found
	 */
	public function getQuestionById($qId) {
		$questionCred;
		$sql = 'SELECT
				id, 
				title, 
				description,
				comment_text,
				required
				FROM ' . self::$templateQuestionTable . '
				 WHERE id = ?';
		
		$statement = $this->dbConnection->runSql($sql, array($qId), "i");

		$result = $statement->bind_result($id, $title, $description, $commentText, $required);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}

		if ($statement->fetch()) {
			$required = $required == 1 ? true : false;
			$commentText = $commentText == 1 ? true : false;
			$questionCred = \form\model\QuestionCredentials::createFull($title, $description, $id, $required, $commentText);
		} else {
			throw new \Exception("Question not found in database");
		}

		$statement->free_result();
        $answers = $this->templateAnswerDAL->getAnswersByQuestion($questionCred);
        $questionCred->addAnswers($answers);

        return $questionCred;
	}

	/**
	 * @param  \form\model\QuestionCredentials  $questionCred
	 * @param  int                      		$formId       
	 * @throws \Exception If database query fails
	 */
	public function updateQuestion(\form\model\QuestionCredentials $questionCred, $formId) {
		$sql = "UPDATE " . self::$templateQuestionTable . "
				SET 
					title = ?,
					description = ?,
					comment_text = ?,
					required = ?
				WHERE id = ? AND form_id = ?";

		$statement = $this->dbConnection->runSql($sql, 
			array($questionCred->getTitle(), $questionCred->getDescription(), $questionCred->commentText(), $questionCred->isRequired(), $questionCred->getId(), $formId), 
			"ssiiii");

		$statement->free_result();
		$this->templateAnswerDAL->updateAnswers($questionCred->getAnswers(), $questionCred->getId());
	}
}