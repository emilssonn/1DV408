<?php

namespace form\model;

/**
 * @author Peter Emilsson
 * Observer for form managment
 */
interface FormObserver {

	public function saveOk($fId = null, $qId = null);
	public function saveFailed($fId = null, $qId = null);
	public function getOk();
	public function getFailed();
	public function deleteOk($fId = null, $qId = null);
	public function deleteFailed($fId = null, $qId = null);
	public function failedToVerify();
	public function notPublic();
	public function notActive();
	public function publishOk($fId);
	public function publishFailed($fId);
	public function noQuestions($fId);
}