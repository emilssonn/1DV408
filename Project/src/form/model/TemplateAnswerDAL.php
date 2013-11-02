<?php

namespace form\model;

require_once("./src/common/model/DbConnection.php");

/**
 * @author Peter Emilsson
 * Responisble for the main actions to the template_answer table
 * All functions with database querys will throw \Exception on error
 */
class TemplateAnswerDAL {

	/**
	 * Table name
	 * @var string
	 */
	private static $templateAnswerTable = "template_answer";

	/**
	 * @var \common\model\DbConnection
	 */
	private $dbConnection;

	/**
	 * @var \user\model\UserCredentials
	 */
	private $user;

	public function __construct(\user\model\UserCredentials $user = null) {
		$this->dbConnection = \common\model\DbConnection::getInstance();
		$this->user = $user;
	}

	/**
	 * Make access public for other DAL classes to use
	 * Ex in JOIN statements
	 * @return string
	 */
	public function getTemplateAnswerTable() {
		return self::$templateAnswerTable;
	}

	/**
	 * @param  form\model\QuestionCredentials $qCred
	 * @return array of \form\model\AnswerCredentials
	 * @throws \Exception If database query fails
	 */
	public function getAnswersByQuestion(\form\model\QuestionCredentials $qCred) {
		$qId = $qCred->getId();
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

        $statement->free_result();
        return $answers;
	}

	/**
	 * @param  array of \form\model\AnswerCredentials $aCreds
	 * @param  int $qId  
	 * @throws \Exception If database query fails 
	 */
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

	/**
	 * @param  array of \form\model\AnswerCredentials $aCreds
	 * @param  int $qId   
	 * @throws \Exception If database query fails
	 */
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