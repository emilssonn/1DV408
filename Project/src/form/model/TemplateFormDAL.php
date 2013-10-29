<?php

namespace form\model;

require_once("./src/common/model/DbConnection.php");
require_once("./src/form/model/TemplateQuestionDAL.php");

class TemplateFormDAL {

	/**
	 * Table name
	 * @var string
	 */
	private static $templateFormTable = "template_form";

	/**
	 * @var \common\model\DbConnection
	 */
	private $dbConnection;

	/**
	 * @var \user\model\UserCredentials
	 */
	private $user;

	/**
	 * @param \user\model\UserCredentials $user
	 */
	public function __construct(\authorization\model\UserCredentials $user = null) {
		$this->dbConnection = \common\model\DbConnection::getInstance();
		$this->user = $user;
	}

	public function getTemplateFormTable() {
		return self::$templateFormTable;
	}

	/**
	 * [userOwnsForm description]
	 * @param  int $id form id
	 * @return bool, true if user owns form
	 * @throws Exception If database query failed or user do not own the form
	 */
	public function userOwnsForm($id) {
		$sql = 'SELECT
				id
				FROM ' . self::$templateFormTable . 
				' WHERE id = ?
				 AND author_id = ?';

		$statement = $this->dbConnection->runSql($sql, array($id, $this->user->getId()), "ii");
		$result = $statement->bind_result($id);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}

		if ($statement->fetch()) {
			return true;
		} else {
			throw new \Exception("User do not have access to this form");
		}
	}

	/**
	 * [getFormById description]
	 * @param  int $id form id
	 * @return \form\model\Form
	 * @throws Exception If database query failed or form not found
	 */
	public function getFormById($id) {
		$sql = 'SELECT
				id, 
				title, 
				description,
				end_date,
				author_id,
				created_date,
				last_updated_date
				FROM ' . self::$templateFormTable . 
				' WHERE id = ?';

		$statement = $this->dbConnection->runSql($sql, array($id), "i");

		$result = $statement->bind_result($id, $title, $description, $endDate, $authorId, $createdDate, $lastUpdateDate);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}

		if ($statement->fetch()) {
			$formCred = \form\model\FormCredentials::createFull($title, $description, $endDate, $authorId, $createdDate, $lastUpdateDate, $id);
			return new \form\model\Form($formCred);
		} else {
			throw new \Exception("Form not found in database");
		}
	}

	/**
	 * [getFullForm description]
	 * @param  int $id form id
	 * @return \form\model\Form
	 * @throws Exception If database query fails
	 */
	public function getFullForm($id) {
		$form = $this->getFormById($id); 
		$templateQuestionDAL = new \form\model\TemplateQuestionDAL();
		$questions = $templateQuestionDAL->getQuestionsByFormId($form->getId());
		$form->addQuestions($questions);
		return $form;
	}

	/**
	 * [getForms description]
	 * @param  boolean $all all forms or only forms created by user
	 * @return array of \form\model\FormCredentials 
	 * @throws Exception If database query fails
	 */
	public function getForms($all = true) {
		$ret = array();
		$sql = 'SELECT
				id, 
				title, 
				description,
				end_date,
				published,
				author_id,
				created_date,
				last_updated_date
				FROM ' . self::$templateFormTable;

		$statement;
		if (!$all) {
			$sql .= ' WHERE author_id = ?
					  ORDER BY published DESC, end_date ASC';
			$statement = $this->dbConnection->runSql($sql, array($this->user->getId()), "i");
		} else {
			$time = date("Y-m-d");
			echo $time;
			$sql .= " WHERE published = ? 
					  AND end_date > '$time'
					  ORDER BY created_date DESC, end_date ASC";
			echo $sql;
			$statement = $this->dbConnection->runSql($sql, array(1), "i");
		}

		$result = $statement->get_result();

        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
                $ret[] = \form\model\FormCredentials::createFull(
                	$object["title"], 
                	$object["description"], 
                	$object["end_date"], 
                	$object["author_id"], 
                	$object["created_date"], 
                	$object["last_updated_date"], 
                	$object["id"],
                	$object["published"]);
        }

        return $ret;
	}

	/**
	 * [insertForm description]
	 * @param  \form\model\FormCredentials $formCred
	 * @return \form\model\FormCredentials $formCred
	 * @throws Exception If database query fails
	 */
	public function insertForm(\form\model\FormCredentials $formCred) {
		$sql = "INSERT INTO " . self::$templateFormTable . "
				(
					title,
					description,
					end_date,
					author_id,
					created_date
				)
				VALUES(?, ?, ?, ?, ?)";

		$statement = $this->dbConnection->runSql($sql, 
			array($formCred->getTitle(), $formCred->getDescription(), $formCred->getEndDate(), $this->user->getId(), null), 
			"sssis");

		$formId = $this->dbConnection->getLastInsertedId();
		$formCred->setId($formId);
		return $formCred;
	}
}