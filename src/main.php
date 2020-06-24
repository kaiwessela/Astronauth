<?php
session_start();

require 'account.php';

class Astronauth {
	public $version = '';
	public $account;
	public $session;


	function __construct() {
		$this->cookiemanager = new CookieManager();
	}

	public function authenticate() {
		try {
			$this->session = Session::read();
		}

		if(!$this->session->is_durable()){
			return true;
		}

		if($this->session->verify_token()){
			$this->session->refresh();
			return true;
		} else {
			$this->session->archive();
			$this->session->unset_cookies();
			return false;
		}
	}

	public function register($data) {
		$this->account = Account::new();
		
		try {
			$this->account->insert($data);
		}

	}

	public function login($identifier, $password, $remember = false) {
		try {
			$this->account = Account::pull($identifier);
		}

		if(!$this->account->verify_password($password)){
			// false
		}

		$this->session = Session::generate((boolean) $remember);
		$this->session->; // HIER WEITER

	}

	public function logout() {
		$this->session->archive();
		$this->session->unset_cookies();
		unset($this->account);
		unset($this->session);
	}
}
?>
