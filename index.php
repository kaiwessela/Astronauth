<?php
session_start();

//require 'backend/exception.php';

//$e = new ExceptionHandler();

require 'backend/config.php';

$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);

require 'backend/account.php';
require 'backend/password.php';
require 'backend/remembrance.php';


class Astronauth {
	private $account;
	private $remembrance;

	private $signedIn = false;

	function __construct() {

	}

	public function initialize() {
		if($this->tryToRemember() == true){
			$this->signedIn = true;
			$this->account = Account::pull($this->remembrance->accountUID);
			$this->remembrance->refresh();
		} else {
			$this->signedIn = false;

			if($this->remembrance instanceof Remembrance){
				$this->remembrance->forget();
			} else {
				Remembrance::unsetCookie();
			}
		}
	}

	public function tryToRemember() {
		$this->remembrance = Remembrance::autoPull();

		if(!$this->remembrance instanceof Remembrance){
			Remebrance::unsetCookie();
			return false;
		}

		return $this->remembrance->verify();
	}

	public function tryToSignIn() {
		$this->account = Account::autoPull();

		if(!$this->account instanceof Account){
			return false;
		} else {
			if($this->account->password->verify() == true){
				$this->signedIn = true;

				if($_POST[POST_REMEMBER_ME] == true){
					$this->remembrance = Remembrance::new($this->account, );
				} else {
					$this->remembrance = Remembrance::new($this->account, REMEMBERME_PERIOD_PERSISTENT);
				}

				$this->remembrance->insert();
				$this->remembrance->setCookie();
			}

			return true;
		}
	}

	public function isAuthenticated() {
		return $this->signedIn;
	}
}
?>
