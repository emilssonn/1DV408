<?php

namespace form\model;

require_once("./src/form/model/FormCredentials.php");
require_once("./src/common/model/DbConnection.php");
require_once("./src/form/model/QuestionResultCredentials.php");
require_once("./src/form/model/AnswerResultCredentials.php");

class FormDAL {

	/**
	 * @var string
	 */
	private static $formTable = "template_form";

	private static $questionTable = "template_question";

	private static $answerTable = "template_answer";

	private static $userFormTable = "user_form";

	private static $userAnswerTable = "user_answer";

	private $dbConnection;

	private $user;

	public function __construct(\authorization\model\UserCredentials $user = null) {
		$this->dbConnection = \common\model\DbConnection::getInstance();
		$this->user = $user;
	}

	/**
	 * [formExists description]
	 * @param  formmodelFormCredentials $formCred [description]
	 * @return [type]                             [description]
	 */
	public function formExists(\form\model\FormCredentials $formCred) {
		try {
			$this->getFormByTitle($formCred);
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function userOwnsForm($id) {
		$sql = 'SELECT
				id
				FROM ' . self::$formTable . 
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
	 * @param  [type] $id [description]
	 * @return [type]     [description]
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
				FROM ' . self::$formTable . 
				' WHERE id = ?';

		$statement = $this->dbConnection->runSql($sql, array($id), "i");

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

	/**
	 * [getFullForm description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getFullForm($id) {
		$form = $this->getFormById($id); 
		$questions = $this->getQuestionsByFormId($form->getId());
		$form->addQuestions($questions);
		return $form;
	}

	private function getQuestionsByFormId($formId) {
		$questions = array();
		$sql = 'SELECT
				id, 
				title, 
				description
				FROM ' . self::$questionTable . '
				 WHERE form_id = ?';
		
		$statement = $this->dbConnection->runSql($sql, array($formId), "i");

		$result = $statement->get_result();

        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
                $questions[] = \form\model\QuestionCredentials::createFormFromDB(
                	$object["title"], 
                	$object["description"], 
                	$object["id"]);
        }

        foreach ($questions as $question) {
        	$answers = $this->getAnswersByQuestion($question);
        	$question->addAnswers($answers);
        }

        return $questions;
	}

	private function getAnswersByQuestion($question) {
		$qId = $question->getId();
		$answers = array();
		$sql = 'SELECT
				id,
				title, 
				type,
				display_order
				FROM ' . self::$answerTable . '
				 WHERE question_id = ?
				 ORDER BY display_order ASC';

		$statement = $this->dbConnection->runSql($sql, array($qId), "i");

		$result = $statement->get_result();
                        
        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
                $answers[] = \form\model\AnswerCredentials::createFormFromDB(
                	$object["title"], 
                	$object["type"], 
                	$object["display_order"],
                	$object["id"]);
        }

        return $answers;
	}

	/**
	 * [getForms description]
	 * @return [type] [description]
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
				FROM ' . self::$formTable;

		$statement;
		if (!$all) {
			$sql .= ' WHERE author_id = ?
					  ORDER BY published DESC, end_date ASC';
			$statement = $this->dbConnection->runSql($sql, array($this->user->getId()), "i");
		} else {
			$statement = $this->dbConnection->runSql($sql);
		}

		$result = $statement->get_result();

        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
                $ret[] = \form\model\FormCredentials::createFormFromDB(
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
	 * @param  formmodelFormCredentials $formCred [description]
	 * @return [type]                             [description]
	 */
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

	/**
	 * [insertQuestion description]
	 * @param  formmodelQuestionCredentials $questionCred [description]
	 * @param  [type]                       $formId       [description]
	 * @return [type]                                     [description]
	 */
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

	public function getQuestionById($qId) {
		$questionCred;
		$sql = 'SELECT
				id, 
				title, 
				description
				FROM ' . self::$questionTable . '
				 WHERE id = ?';
		
		$statement = $this->dbConnection->runSql($sql, array($qId), "i");

		$result = $statement->bind_result($id, $title, $description);
		if ($result == FALSE) {
			throw new \Exception("bind of [$sql] failed " . $statement->error);
		}

		if ($statement->fetch()) {
			$questionCred = \form\model\QuestionCredentials::createFormFromDB($title, $description, $id);
		} else {
			throw new \Exception("Question not found in database");
		}

		$statement->free_result();
        $answers = $this->getAnswersByQuestion($questionCred);
        $questionCred->addAnswers($answers);

        return $questionCred;
	}

	/**
	 * [insertAnsweredForm description]
	 * @param  formmodelForm $form                       [description]
	 * @param  [type]        $answerViewCredentialsArray [description]
	 * @return [type]                                    [description]
	 */
	public function insertAnsweredForm(\form\model\Form $form, $answerViewCredentialsArray) {
		$id = $this->insertUserForm($form);
		$this->insertUserAnswers($answerViewCredentialsArray, $id);
	}

	private function insertUserAnswers($answerViewCredentialsArray, $id) {
		$sql = "INSERT INTO " . self::$userAnswerTable . "
				(
					user_form_id,
					question_id,
					answer_id
				)
				VALUES(?, ?, ?)";

		foreach ($answerViewCredentialsArray as $aCred) {
			$statement = $this->dbConnection->runSql($sql, 
			array($id, $aCred->getQuestionId(), $aCred->getAnswerId()),
			"iii");
		}
	}

	private function insertUserForm(\form\model\Form $form) {
		$sql = "INSERT INTO " . self::$userFormTable . "
				(
					user_id,
					form_id
				)
				VALUES(?, ?)";

		$statement = $this->dbConnection->runSql($sql, 
			array($this->user->getId(), $form->getId()),
			"ii");

		return $this->dbConnection->getLastInsertedId();
	}

	public function getFormResult(\form\model\Form $form) {
		$questionResultsArray = array();
		foreach ($form->getQuestions() as $question) {
			$userAnswerTable = self::$userAnswerTable;
			$answerTable = self::$answerTable;
			$sql = "SELECT 
				$userAnswerTable.answer_id,
				COUNT(1) as amount,
				$answerTable.title
				FROM $userAnswerTable
				INNER JOIN $answerTable
					ON $userAnswerTable.answer_id = $answerTable.id 
				WHERE $userAnswerTable.question_id = ? 
				GROUP BY $userAnswerTable.answer_id";
			
			$qId = $question->getId();
			$qTitle = $question->getTitle();
			$qDescription = $question->getDescription();

			$statement = $this->dbConnection->runSql($sql, 
					array($qId),
					"i");

			$result = $statement->get_result();
            
            $questionResultCred = new \form\model\QuestionResultCredentials($qId, $qTitle, $qDescription);
	        while ($object = $result->fetch_array(MYSQLI_ASSOC)) {
	        	$questionResultCred->addAnswerResult(
	        		new \form\model\AnswerResultCredentials(
	        			$object['answer_id'], $object['amount'], $object['title']));
	        }
	        $questionResultsArray[] = $questionResultCred;
		}

		return $questionResultsArray;
	}
}