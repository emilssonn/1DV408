<?php

namespace form\view;

require_once("./src/form/model/FormObserver.php");
require_once("./src/common/Filter.php");
require_once("./src/common/view/UserMessage.php");
require_once("./src/common/model/MessageHandler.php");

/**
 * @author Peter Emilsson
 * Common class for all view in \form\view
 * All views in \form\view extends this class
 */
abstract class FormView implements \form\model\FormObserver {

	/**
	 * @var \application\view\Navigation
	 */
	protected $navigationView;

	/**
	 * @param \application\view\Navigation $navigationView
	 */
	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
	}

	/**
	 * @return boolean true if Request method is post
	 */
	public function isSubmitning() {
		return strtolower($_SERVER['REQUEST_METHOD']) == "post";
	}

	/**
	 * @return int
	 * @throws \Exception If no form id is found in url
	 */
	public function getFormId() {
		$idGET = $this->navigationView->getForm();
		if (empty($_GET[$idGET]))
			throw new \Exception('No form id in url');
		return $_GET[$idGET];
	}

	/**
	 * Save a pointer to a message, will be displayed on next request
	 * @param  int $messageKey
	 */
	protected function saveMessage($messageKey) {
		$messageHandler = new \common\model\MessageHandler();
		$messageHandler->addMessage($messageKey);
	}

	/**
	 * Displays all messages from last request and then removes them
	 * @return String HTML, empty string if no messages
	 */
	protected function displayMessages() {
		$messageHandler = new \common\model\MessageHandler();
		$messages = $messageHandler->load();
		if (count($messages) > 0) {
			$html = "
				<div class='alert alert-info alert-dismissable action-alert'>
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
					<ul>";

			foreach ($messages as $messageKey) {
				$message = \common\view\UserMessage::getMessageByKey($messageKey);
				$html .= "
						<li>
							$message
						</li>";
			}

			$html .= "
					</ul>
				</div>";

			$messageHandler->removeAll();
			return $html;
		}
		return "";
	}

	/**
	 * Observer implementations, child view overrides the ones it uses
	 */
	public function saveOk($fId = null, $qId = null) {

	}

	public function saveFailed($fId = null, $qId = null) {

	}

	public function getOk() {

	}

	public function getFailed() {

	}

	public function deleteOk($fId = null, $qId = null) {

	}

	public function deleteFailed($fId = null, $qId = null) {

	}

	public function notPublic() {

	}

	public function notActive() {
		
	}

	public function publishOk($fId) {

	}

	public function publishFailed($fId) {
		
	}

	public function noQuestions($fId) {

	}

	/**
	 * User to not have access to the requested data
	 * Redirect to home
	 */
	public function failedToVerify() {
		$this->saveMessage(1001);
		$this->navigationView->goToHome();
		exit();//Exit script
	}
}