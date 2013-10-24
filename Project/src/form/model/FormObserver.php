<?php

namespace form\model;

interface FormObserver {

	public function addFormOk(\form\model\FormCredentials $formCred);
	public function addFormFailed();
	public function getFormOk();
	public function getFormFailed();
}