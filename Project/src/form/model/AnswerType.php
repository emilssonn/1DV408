<?php

namespace form\model;

require_once("./src/vendor/purplekiwienum.php");

/**
 * @author Peter Emilsson
 * The diffrent types of answers that is possible to create
 */
class AnswerType extends \PurpleKiwi\Enum {
	const Input = 1;
	const Text = 2;
}