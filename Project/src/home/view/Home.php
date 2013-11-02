<?php

namespace home\view;

require_once("./src/common/view/UserMessage.php");
require_once("./src/common/model/MessageHandler.php");

/**
 * @author Peter Emilsson
 */
class Home {

	/**
	 * @var \application\view\Navigation $navigationView
	 */
	private $navigationView;

	public function __construct(\application\view\Navigation $navigationView) {
		$this->navigationView = $navigationView;
	}

	/**
	 * @return string HTML
	 */
	public function getHTML() {
		$html = $this->displayMessages();
		$html .= "<h1>Home</h1>";
		return $html;
	}

	/**
	 * @param  int $messageKey           
	 */
	private function saveMessage($messageKey) {
		$messageHandler = new \common\model\MessageHandler();
		$messageHandler->addMessage($messageKey);
	}

	/**
	 * @return string HTML
	 */
	private function displayMessages() {
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
}