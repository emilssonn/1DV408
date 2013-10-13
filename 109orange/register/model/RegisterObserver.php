<?php

namespace register\model;

/**
 * Callback interface
 */
interface RegisterObserver {
	public function registerFailed();
	public function registerOK();
	public function userExists();
}

