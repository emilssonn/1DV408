<?php

namespace form\view;

require_once("./src/common/Filter.php");
require_once("./src/form/view/FormView.php");

/**
 * @author Peter Emilsson
 * Abstract class used for creating forms and questions
 */
abstract class Create extends \form\view\FormView {
	
	/**
	 * Title in POST
	 * @var string
	 */
	protected static $titlePOST = "View::Form::Title";

	/**
	 * Description in POST
	 * @var string
	 */
	protected static $descriptionPOST = "View::Form::Description";

	/**
	 * @var string
	 */
	protected $titleErrorMessage = null;

	/**
	 * @var string
	 */
	protected $descriptionErrorMessage = null;

	/**
	 * @param  string $title
	 * @return string HTML
	 */
	protected function getTitleTag($minLength, $maxLength, $title = null) {
		if ($title == null)
			$title = $this->getTitle();
		$errorClass = "";
		$errorMessage = "";
		$autoFocus = "";
		if ($this->titleErrorMessage != null) {
			$errorClass = "has-error";
			$errorMessage = $this->titleErrorMessage;
			$autoFocus = "autofocus";
		}
		return "
			<div class='form-group $errorClass'>
				<label for='titleID' class='control-label'>Title: $errorMessage</label>
				<input type='text' value='$title' name='" . self::$titlePOST . "' id='titleID' 
					class='form-control' placeholder='Title' $autoFocus data-validation='length' 
					data-validation-length='$minLength-$maxLength' maxlength='$maxLength'>
			</div>";
	}

	/**
	 * @param  boolean $optional   
	 * @param  string  $description
	 * @return string HTML
	 */
	protected function getDescriptionTag($minLength, $maxLength, $optional = false, $description = null) {
		if ($description == null)
			$description = $this->getDescription();
		$errorClass = "";
		$errorMessage = "";
		$autoFocus = "";
		$optionalText = "";
		$optionalData = "";
		if ($optional) {
			$optionalText = "(Optional)";
			$optionalData = "data-validation-optional='true'";
		}
		if ($this->descriptionErrorMessage != null) {
			$errorClass = "has-error";
			$errorMessage = $this->descriptionErrorMessage;
			$autoFocus = "autofocus";
		}
		return "
			<div class='form-group $errorClass'>
				<label for='descriptionId' class='control-label'>Description: $optionalText $errorMessage</label>
				<textarea name='" . self::$descriptionPOST . "' id='descriptionId' 
					class='form-control' placeholder='Description' $autoFocus $optionalData maxlength='$maxLength' 
					data-validation='length' data-validation-length='$minLength-$maxLength' rows='4'>$description</textarea>
			</div>";
	}

	/**
	 * Sets title error message
	 * @param  int $minLength 
	 * @param  int $maxLength
	 */
	public function titleError($minLength, $maxLength) {
		$this->titleErrorMessage = "Accepted length: $minLength-$maxLength characters";
	}

	/**
	 * Sets Description error message
	 * @param  int $minLength 
	 * @param  int $maxLength
	 */
	public function descriptionError($minLength, $maxLength) {
		$this->descriptionErrorMessage = "Accepted length: $minLength-$maxLength characters";
	}

	/**
	 * @return string
	 */
	protected function getTitle() {
		if (isset($_POST[self::$titlePOST]))
			return \common\Filter::sanitizeString($_POST[self::$titlePOST]);
		else
			return "";
	}

	/**
	 * @return string
	 */
	protected function getDescription() {
		if (isset($_POST[self::$descriptionPOST]))
			return \common\Filter::sanitizeString($_POST[self::$descriptionPOST]);
		else
			return "";
	}
}