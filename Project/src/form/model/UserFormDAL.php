<?php

namespace form\model;

require_once("./src/common/model/DbConnection.php");
require_once("./src/form/model/UserAnswerDAL.php");
require_once("./src/form/model/UserQuestionCredentials.php");
require_once("./src/form/model/SubmittedFormCredentials.php");

class UserFormDAL {

	private static $userFormTable = "user_form";

	private $dbConnection;

	private $user;

	private $userAnswerDAL;

	public function __construct(\authorization\model\UserCredentials $user = null) {
		$this->dbConnection = \common\model\DbConnection::getInstance();
		$this->user = $user;
		$this->userAnswerDAL = new \form\model\UserAnswerDAL();
	}

	public function getFormResult(\form\model\Form $form) {
		return $this->userAnswerDAL->getFormResult($form);
	}

	public function getSubmittedFormsByUser(\form\model\TemplateFormDAL $templateFormDAL, $one = null) {
		$templateFormTable = $templateFormDAL->getTemplateFormTable();
		$userFormTable = self::$userFormTable;
		$ret = array();
		$sql = "SELECT 
				$userFormTable.id,
				$userFormTable.form_id,
				$userFormTable.last_updated_date,
				$userFormTable.submitted_date,
				$templateFormTable.title,
				$templateFormTable.description,
				$templateFormTable.end_date,
				$templateFormTable.author_id
				FROM $userFormTable
				INNER JOIN $templateFormTable
					ON $userFormTable.form_id = $templateFormTable.id 
				WHERE $userFormTable.user_id = ? ";

		if ($one != null) {
			$sql .= "AND $userFormTable.id = ? ";
		} 

		$sql .= "GROUP BY $userFormTable.form_id 
				ORDER BY $userFormTable.last_updated_date";

		if ($one != null) {
			$statement = $this->dbConnection->runSql($sql, 
				array($this->user->getId(), $one),
				"ii");
		} else {
			$statement = $this->dbConnection->runSql($sql, 
				array($this->user->getId()),
				"i");
		}
            
        $result = $statement->get_result();

        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
                $ret[] = new \form\model\SubmittedFormCredentials(
                	$object["form_id"],
                	$object["id"],
                	$object["title"], 
                	$object["description"],   	
                	$object["author_id"], 
                	$object["end_date"], 
                	$object["submitted_date"], 
                	$object["last_updated_date"]);
        }
		$statement->free_result();

		return $ret;
	}

	public function getFormResultByUser($subFormId, \form\model\TemplateFormDAL $templateFormDAL) {
		$submittedFormCredentials = $this->getSubmittedFormsByUser($templateFormDAL, $subFormId);

		$submittedFormCredentials[0]->addAnswersResult($this->userAnswerDAL->getUserFormResult($subFormId));

		return $submittedFormCredentials[0];
	}

		/**
	 * [insertAnsweredForm description]
	 * @param  formmodelForm $form                       [description]
	 * @param  [type]        $answerViewCredentialsArray [description]
	 * @return [type]                                    [description]
	 */
	public function insertAnsweredForm(\form\model\Form $form, $answerViewCredentialsArray) {
		$id = $this->insertUserForm($form);
		$this->userAnswerDAL->insertUserAnswers($answerViewCredentialsArray, $id);
	}

	public function updateAnsweredForm($submittedFormCredentials, $answers) {
		$this->updateAnsweredFormDate($submittedFormCredentials);
		$this->userAnswerDAL->updateUserAnswers($submittedFormCredentials, $answers);
	}

	private function updateAnsweredFormDate(\form\model\SubmittedFormCredentials $submittedFormCredentials) {
		$sql = "UPDATE " . self::$userFormTable . "
				SET last_updated_date = null
				WHERE id = ? 
				AND user_id = ? 
				AND form_id = ?";

		$userId = $this->user->getId();
		$formId = $submittedFormCredentials->getFormId();
		$userFormId = $submittedFormCredentials->getUserFormId();
		$statement = $this->dbConnection->runSql($sql, 
			array($userFormId, $userId, $formId),
			"iii");
		$statement->free_result();
	}

	private function insertUserForm(\form\model\Form $form) {
		$sql = "INSERT INTO " . self::$userFormTable . "
				(
					user_id,
					form_id,
					submitted_date
				)
				VALUES(?, ?, ?)";

		$statement = $this->dbConnection->runSql($sql, 
			array($this->user->getId(), $form->getId(), null),
			"iis");

		return $this->dbConnection->getLastInsertedId();
	}
}