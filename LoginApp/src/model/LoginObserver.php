<?php

namespace model;

interface LoginObserver {
	
	public function okFormLogin();

	public function okCookieLogin();

	public function okLogOut();

	public function okKeepMeLoggedIn();

	public function failedCookieLogin();

	public function wrongUserCredentials();
}