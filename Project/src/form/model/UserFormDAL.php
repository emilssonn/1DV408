<?php

namespace form\model;

require_once("./src/common/model/DbConnection.php");
require_once("./src/form/model/UserAnswerDAL.php");
require_once("./src/form/model/SubmittedFormCredentials.php");

/**
 * @author Peter Emilsson
 * Class used to access the table "user_form"
 * All functions throws \Exception on error
 */
class UserFormDAL {

	/**
	 * @var string
	 */
	private static $userFormTable = "user_form";

	/**
	 * @var \common\model\DbConnection
	 */
	private $dbConnection;

	/**
	 * @var \user\model\UserCredentials
	 */
	private $user;

	/**
	 * @var \form\model\UserAnswerDAL
	 */
	private $userAnswerDAL;

	/**
	 * @param \user\model\UserCredentials $user, logged in user
	 */
	public function __construct(\user\model\UserCredentials $user = null) {
		$this->dbConnection = \common\model\DbConnection::getInstance();
		$this->user = $user;
		$this->userAnswerDAL = new \form\model\UserAnswerDAL();
	}

	/**
	 * @param  \form\model\Form $form
	 * @return array of \form\model\QuestionResultCredentials
	 * @throws \Exception If database query fails        
	 */
	public function getFormResult(\form\model\Form $form) {
		return $this->userAnswerDAL->getFormResult($form);
	}

	/**
	 * @param  \form\model\TemplateFormDAL 	$templateFormDAL
	 * @param  int                   		$userFormTableId, optional - return only one if provided
	 * @return array of \form\model\SubmittedFormCredentials
	 * @throws \Exception If database query fails        
	 */
	public function getSubmittedFormsByUser(\form\model\TemplateFormDAL $templateFormDAL,
											$userFormTableId = null) {
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

		if ($userFormTableId != null) {
			$sql .= "AND $userFormTable.id = ? ";
		} 

		$sql .= " 
				ORDER BY $userFormTable.last_updated_date DESC";

		if ($userFormTableId != null) {
			$stmt = $this->dbConnection->runSql($sql, 
				array($this->user->getId(), $userFormTableId),
				"ii");
		} else {
			$stmt = $this->dbConnection->runSql($sql, 
				array($this->user->getId()),
				"i");
		}

        $result = $stmt->get_result();

        $userDAL = new \user\model\UserDAL();
        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
			$author = $userDAL->getUserById($object["author_id"]);
            $ret[] = new \form\model\SubmittedFormCredentials(
              	$object["form_id"],
               	$object["id"],
               	$object["title"], 
               	$object["description"],   	
               	$author, 
                new \common\model\CustomDateTime($object["end_date"]), 
               	new \common\model\CustomDateTime($object["submitted_date"]), 
               	new \common\model\CustomDateTime($object["last_updated_date"]));
        }

		$stmt->free_result();
		return $ret;
	}

	/**
	 * @param  int                   		$subFormId
	 * @param  \form\model\TemplateFormDAL 	$templateFormDAL 
	 * @return \form\model\SubmittedFormCredentials
	 * @throws \Exception If database query fails        
	 */
	public function getFormResultByUser($subFormId, \form\model\TemplateFormDAL $templateFormDAL) {
		$submittedFormCredentials = $this->getSubmittedFormsByUser($templateFormDAL, $subFormId);

		$submittedFormCredentials[0]->addAnswersResult($this->userAnswerDAL->getUserFormResult($subFormId));

		return $submittedFormCredentials[0];
	}

	/**
	 * @param  \form\model\Form 							$form                      
	 * @param  array of \form\model\AnswerViewCredentials 	$answerViewCredentialsArray
	 * @return int the id given by the database
	 * @throws \Exception If database query fails        
	 */
	public function insertAnsweredForm(\form\model\Form $form, $answerViewCredentialsArray) {
		$id = $this->insertUserForm($form);
		$this->userAnswerDAL->insertUserAnswers($answerViewCredentialsArray, $id);
		return $id;
	}

	/**
	 * @param  \form\model\SubmittedFormCredentials $submittedFormCredentials
	 * @param  \form\model\QuestionViewCredentials $questionViewCredentialsArray
	 * @return int                  
	 * @throws \Exception If database query fails               
	 */
	public function updateAnsweredForm(\form\model\SubmittedFormCredentials $submittedFormCredentials, 
										$questionViewCredentialsArray) {
		$this->updateAnsweredFormDate($submittedFormCredentials);
		$this->userAnswerDAL->updateUserAnswers($submittedFormCredentials, $questionViewCredentialsArray);
		return $submittedFormCredentials->getUserFormId();
	}

	/**
	 * @param  \form\model\SubmittedFormCredentials $submittedFormCredentials
	 * @throws \Exception If database query fails        
	 */
	private function updateAnsweredFormDate(\form\model\SubmittedFormCredentials $submittedFormCredentials) {
		$sql = "UPDATE " . self::$userFormTable . "
				SET last_updated_date = null
				WHERE id = ? 
				AND user_id = ? 
				AND form_id = ?";

		$userId = $this->user->getId();
		$formId = $submittedFormCredentials->getFormId();
		$userFormId = $submittedFormCredentials->getUserFormId();
		$this->dbConnection->runSql($sql, 
			array($userFormId, $userId, $formId),
			"iii");
	}

	/**
	 * @param  \form\model\Form $form
	 * @return int id given by the database
	 * @throws \Exception If database query fails        
	 */
	private function insertUserForm(\form\model\Form $form) {
		$sql = "INSERT INTO " . self::$userFormTable . "
				(
					user_id,
					form_id,
					submitted_date
				)
				VALUES(?, ?, ?)";

		$this->dbConnection->runSql($sql, 
			array($this->user->getId(), $form->getId(), null),
			"iis");

		return $this->dbConnection->getLastInsertedId();
	}
}