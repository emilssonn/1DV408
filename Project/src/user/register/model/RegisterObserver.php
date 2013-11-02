<?php

namespace user\register\model;

/**
 * @author Peter Emilsson
 * Callback interface
 */
interface RegisterObserver {
	public function registerFailed();
	public function registerOK();
	public function userExists();
}

