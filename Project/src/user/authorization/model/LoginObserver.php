<?php

namespace user\authorization\model;

interface LoginObserver {
	
	public function loginFailed();
	public function loginOK(\user\model\TemporaryPasswordServer $info, $rememberMe);
}