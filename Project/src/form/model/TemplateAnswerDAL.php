<?php

namespace form\model;

require_once("./src/common/model/DbConnection.php");

class TemplateAnswerDAL {

	private static $templateAnswerTable = "template_answer";

	private $dbConnection;

	private $user;

	public function __construct(\authorization\model\UserCredentials $user = null) {
		$this->dbConnection = \common\model\DbConnection::getInstance();
		$this->user = $user;
	}

	public function getTemplateAnswerTable() {
		return self::$templateAnswerTable;
	}

	public function getAnswersByQuestion($question) {
		$qId = $question->getId();
		$answers = array();
		$sql = 'SELECT
				id,
				title, 
				type,
				display_order
				FROM ' . self::$templateAnswerTable . '
				 WHERE question_id = ?
				 ORDER BY display_order ASC';

		$statement = $this->dbConnection->runSql($sql, array($qId), "i");

		$result = $statement->get_result();
                        
        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
                $answers[] = \form\model\AnswerCredentials::createFull(
                	$object["title"], 
                	$object["type"], 
                	$object["display_order"],
                	$object["id"]);
        }

        return $answers;
	}

	public function insertAnswers($aCreds, $qId) {
		$sql = "INSERT INTO " . self::$templateAnswerTable . "
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

	public function updateAnswers($aCreds, $qId) {
		$sql = "UPDATE " . self::$templateAnswerTable . "
				SET
					title = ?,
					type = ?,
					display_order = ?
				WHERE id = ? AND question_id = ?";

		foreach ($aCreds as $aCred) {
			$statement = $this->dbConnection->runSql($sql, 
			array($aCred->getTitle(), $aCred->getType(), $aCred->getOrder(), $aCred->getId(), $qId),
			"ssiii");
		}
	}

}