<?php

namespace form\model;

require_once("./src/common/model/DbConnection.php");
require_once("./src/form/model/TemplateQuestionDAL.php");
require_once("./src/form/model/FormCollection.php");
require_once("./src/common/model/CustomDateTime.php");

/**
 * @author Peter Emilsson
 * Responisble for the main actions to the template_form table
 * All functions with database querys will throw \Exception on error
 */
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
	public function __construct(\user\model\UserCredentials $user = null) {
		$this->dbConnection = \common\model\DbConnection::getInstance();
		$this->user = $user;
	}

	/**
	 * Make access public for other DAL classes to use
	 * Ex in JOIN statements
	 * @return string
	 */
	public function getTemplateFormTable() {
		return self::$templateFormTable;
	}

	/**
	 * @param  int $formId 
	 * @throws \Exception If database query fails
	 */
	public function deleteForm($formId) {
		$sql = "DELETE
				FROM " . self::$templateFormTable . " 
				WHERE id = ?";

		$this->dbConnection->runSql($sql, array($formId), "i");
	}

	/**
	 * @param  int $formId
	 * @return bool
	 */
	public function formHasQuestions($formId) {
		$templateQuestionDAL = new \form\model\TemplateQuestionDAL();
		$table = $templateQuestionDAL->getTemplateQuestionTable();
		$sql = "SELECT
				COUNT(1)
				FROM $table
				WHERE form_id = ?";

		$statement = $this->dbConnection->runSql($sql, array($formId), "i");
		$result = $statement->bind_result($count);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}
		if ($statement->fetch()) {
			if ($count == null || $count == 0)
				return false;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param  int $id form id
	 * @return bool, true if user owns form
	 * @throws \Exception If database query failed or user do not own the form
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
	 * @param  int $id form id
	 * @return \form\model\Form
	 * @throws \Exception If database query failed or form not found
	 */
	public function getFormById($id) {
		$sql = "SELECT
				id, 
				title, 
				description,
				date_format(end_date, '%Y-%m-%d %H:%i'),
				author_id,
				created_date,
				last_updated_date,
				published
				FROM " . self::$templateFormTable . 
				" WHERE id = ?";

		$statement = $this->dbConnection->runSql($sql, array($id), "i");

		$result = $statement->bind_result($id, $title, $description, $endDate, $authorId, $createdDate, $lastUpdateDate, $published);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}

		if ($statement->fetch()) {
			$userDAL = new \user\model\UserDAL();
			$statement->free_result();
			$author = $userDAL->getUserById($authorId);
			return \form\model\Form::createFull(
				$title, 
				$description, 
				new \common\model\CustomDateTime($endDate), 
				$author, 
				new \common\model\CustomDateTime($createdDate), 
				new \common\model\CustomDateTime($lastUpdateDate), 
				$id, 
				$published);
		} else {
			throw new \Exception("Form not found in database");
		}
	}

	/**
	 * @param  int $id form id
	 * @return \form\model\Form
	 * @throws \Exception If database query fails
	 */
	public function getFullForm($id) {
		$form = $this->getFormById($id); 
		$templateQuestionDAL = new \form\model\TemplateQuestionDAL();
		$questions = $templateQuestionDAL->getQuestionsByFormId($form->getId());
		$form->addQuestions($questions);
		return $form;
	}

	/**
	 * @param  boolean $all all forms or only forms created by user
	 * @return \form\model\FormCollection
	 * @throws \Exception If database query fails
	 */
	public function getForms($all = true) {
		$formCollection = new \form\model\FormCollection();
		$sql = "SELECT
				id, 
				title, 
				description,
				end_date,
				published,
				author_id,
				created_date,
				last_updated_date
				FROM " . self::$templateFormTable;

		$statement;
		if (!$all) {
			$sql .= ' WHERE author_id = ?
					  ORDER BY published DESC, end_date ASC';
			$statement = $this->dbConnection->runSql($sql, array($this->user->getId()), "i");
		} else {
			$time = Date("Y-m-d H:i");
			$sql .= " WHERE published = ? 
					  AND end_date > '$time'
					  ORDER BY created_date DESC, end_date ASC
					  LIMIT 500";//Hardcoded limit, bad
			$statement = $this->dbConnection->runSql($sql, array(1), "i");
		}

		$result = $statement->get_result();

		$userDAL = new \user\model\UserDAL();
        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
			$author = $userDAL->getUserById($object["author_id"]);
            $formCollection->addForm(\form\model\Form::createFull(
             	$object["title"], 
               	$object["description"], 
               	new \common\model\CustomDateTime($object["end_date"]), 
               	$author, 
               	new \common\model\CustomDateTime($object["created_date"]), 
               	new \common\model\CustomDateTime($object["last_updated_date"]), 
               	$object["id"],
               	(bool)$object["published"]));
        }
        $statement->free_result();
        return $formCollection;
	}

	/**
	 * @param  \form\model\Form $form
	 * @return int Form id from database
	 * @throws \Exception If database query fails
	 */
	public function insertForm(\form\model\Form $form) {
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
			array($form->getTitle(), $form->getDescription(), $form->getEndDate(), $this->user->getId(), null), 
			"sssis");
		
		$statement->free_result();
		return $this->dbConnection->getLastInsertedId();
	}

	/**
	 * @param  \form\model\Form $form 
	 * @throws \Exception If database query fails
	 */
	public function updateForm(\form\model\Form $form) {
		$sql = "UPDATE " . self::$templateFormTable . "
				SET 
					title = ?,
					description = ?,
					end_date = ?
				WHERE id = ? AND author_id = ?";

		$statement = $this->dbConnection->runSql($sql, 
			array($form->getTitle(), $form->getDescription(), $form->getEndDate(), $form->getId(), $this->user->getId()), 
			"sssii");

		$statement->free_result();
	}

	/**
	 * Change the published column to false or true
	 * @param  int $formId
	 * @throws \Exception If database query fails
	 */
	public function publishForm($formId) {
		$sql = "UPDATE " . self::$templateFormTable . "
				SET published = !published
				WHERE id = ?";

		$statement = $this->dbConnection->runSql($sql, 
			array($formId), 
			"i");
		$statement->free_result();
	}
}