<?php

namespace authorization\model;

interface LoginObserver {
	
	public function loginFailed();
	public function loginOK(\authorization\model\TemporaryPasswordServer $info, $rememberMe);
}