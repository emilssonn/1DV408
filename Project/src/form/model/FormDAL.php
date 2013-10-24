<?php

namespace form\model;

require_once("./src/form/model/FormCredentials.php");
require_once("./src/common/model/DbConnection.php");

class FormDAL {

	/**
	 * @var string
	 */
	private static $formTable = "form";

	private static $questionTable = "form_question";

	private static $answerTable = "question_answer";

	private $dbConnection;

	private $user;

	public function __construct(\authorization\model\UserCredentials $user = null) {
		$this->dbConnection = \common\model\DbConnection::getInstance();
		$this->user = $user;
	}

	public function formExists(\form\model\FormCredentials $formCred) {
		try {
			$this->getFormByTitle($formCred);
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function getFormByTitle(\form\model\FormCredentials $formCred) {
		$sql = 'SELECT
				id, 
				title, 
				description,
				end_date,
				author_id,
				created_date,
				last_updated_date
				FROM ' . self::$formTable . 
				' WHERE title = ?';

		$statement = $this->dbConnection->runSql($sql, array($formCred->getTitle()), "s");

		$result = $statement->bind_result($id, $title, $description, $endDate, $authorId, $createdDate, $lastUpdateDate);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}

		if ($statement->fetch()) {
			return \form\model\FormCredentials::createFormFromDB($title, $description, $endDate, $authorId, $createdDate, $lastUpdateDate, $id);
		} else {
			throw new \Exception("Form not found in database");
		}
	}

	public function getFormById($id) {
		$sql = 'SELECT
				id, 
				title, 
				description,
				end_date,
				author_id,
				created_date,
				last_updated_date
				FROM ' . self::$formTable . 
				' WHERE id = ? 
				 AND author_id = ?';

		$statement = $this->dbConnection->runSql($sql, array($id, $this->user->getId()), "ii");

		$result = $statement->bind_result($id, $title, $description, $endDate, $authorId, $createdDate, $lastUpdateDate);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}

		if ($statement->fetch()) {
			$formCred = \form\model\FormCredentials::createFormFromDB($title, $description, $endDate, $authorId, $createdDate, $lastUpdateDate, $id);
			return new \form\model\Form($formCred);
		} else {
			throw new \Exception("Form not found in database");
		}
	}

	public function insertForm(\form\model\FormCredentials $formCred) {
		$sql = "INSERT INTO " . self::$formTable . "
				(
					title,
					description,
					end_date,
					author_id
				)
				VALUES(?, ?, ?, ?)";

		$statement = $this->dbConnection->runSql($sql, 
			array($formCred->getTitle(), $formCred->getDescription(), $formCred->getEndDate(), $this->user->getId()), 
			"sssi");

		$formId = $this->dbConnection->getLastInsertedId();
		$formCred->setId($formId);
		return $formCred;
	}

	public function insertQuestion(\form\model\QuestionCredentials $questionCred, $formId) {
		$sql = "INSERT INTO " . self::$questionTable . "
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
		$this->insertAnswers($questionCred->getAnswers(), $questionId);
	}

	private function insertAnswers($aCreds, $qId) {
		$sql = "INSERT INTO " . self::$answerTable . "
				(
					question_id,
					title,
					type,
					display_order
				)
				VALUES(?, ?, ?, ?)";

		foreach ($aCreds as $aCred) {
			$statement = $this->dbConnection->runSql($sql, 
			array($qId, $aCred->getTitle(), $aCred->getType(), $aCred->getOrder()),
			"issi");
		}
	}
}