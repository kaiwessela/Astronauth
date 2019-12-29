<?php

require 'exception.php';

$e = new ExceptionHandler();

require 'config.php';

$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);

require 'account.php';
require 'password.php';
require 'remembrance.php';


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
			Remembrance::unsetCookie();
			return false;
		}

		return $this->remembrance->verify();
	}

	public function tryToSignIn() {
		$this->account = Account::autoPull();

		if(!$this->account instanceof Account){
			return false;
		} else {
			if($this->account->password->autoVerify() == true){
				$this->signedIn = true;

				if($_POST[POST_REMEMBER_ME] == true){
					$this->remembrance = Remembrance::new($this->account, REMEMBERME_PERIOD_SESSION);
				} else {
					$this->remembrance = Remembrance::new($this->account, REMEMBERME_PERIOD_PERSISTENT);
				}

				$this->remembrance->insert();
				$this->remembrance->setCookie();

				return true;
			} else {
				return false;
			}
		}
	}

	public function signUp() {
		global $e;
		$e->throw(new AstroEx(0, 'main.signUp.begin', 'Signup attempt started'));

		if($this->isAuthenticated()){
			$e->throw(new AstroEx(3, 'main.signUp.alreadyAuthenticated', 'Signup attempt failed because of existing authentication'));
			return false;
		}

		$this->account = Account::new();
		if($this->account->autoSetUsername() != true){
			$e->throw(new AstroEx(3, 'main.signUp.setUsernameFailed', 'Signup attempt failed because username could not be set'));
			return false;
		}

		if($this->account->autoSetEmail() != true){
			$e->throw(new AstroEx(3, 'main.signUp.setEmailFailed', 'Signup attempt failed because email could not be set'));
			return false;
		}

		$password = Password::new($this->account);
		if($password->autoHash() != true){
			$e->throw(new AstroEx(3, 'main.signUp.passwordHashFailed', 'Signup attempt failed because password hash could no be set'));
			return false;
			echo serialize($this->account);
		}

		if($password->insert() != true) {
			$e->throw(new AstroEx(3, 'main.signUp.passwordInsertFailed', 'Signup attempt failed because password could not be inserted into database'));
			return false;
		}

		$this->account->password = $password;

		if($this->account->insert() != true) {
			$e->throw(new AstroEx(1, 'main.signUp.debug.account', json_encode($this->account)));
			$e->throw(new AstroEx(3, 'main.signUp.accountInsertFailed', 'Signup attempt failed because account could not be inserted into database'));
			$password->delete();
			return false;
		} else {
			$e->throw(new AstroEx(0, 'main.signUp.complete', 'Signup attempt completed successfully'));
			return true;
		}
	}

	public function signOut() {
		if($this->remembrance instanceof Remembrance){
			$this->remembrance->forget();
		} else {
			Remembrance::unsetCookie();
		}

		$this->signedIn = false;
	}

	public function isAuthenticated() {
		return $this->signedIn;
	}
}
?>
