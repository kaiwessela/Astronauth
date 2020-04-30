<?php
$e->info('main.loading');

require 'exception.php';

$e = new ExceptionHandler();

require 'config.php';

$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
$e->debug('pdo', serialize($pdo));

require 'account.php';
require 'password.php';
require 'remembrance.php';


class Astronauth {
	private $account;
	private $remembrance;

	private $signedIn = false;

	function __construct() {
		global $e;
		$e->info('astronauth.instance.construct');
	}

	public function initialize() {
		global $e;
		$e->info('astronauth.initialize.begin');

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
		global $e;
		$e->info('astronauth.tryToRemember.begin');

		$this->remembrance = Remembrance::autoPull();

		if(!$this->remembrance instanceof Remembrance){
			Remembrance::unsetCookie();
			return false;
		}

		return $this->remembrance->verify();
	}

	public function tryToSignIn() {
		global $e;
		$e->info('astronauth.tryToSignIn.begin');

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
		$e->info('astronauth.signUp.begin');

		if($this->isAuthenticated()){
			$e->error('astronauth.signUp.alreadyAuthenticated');
			return false;
		}

		$this->account = Account::new();
		if($this->account->autoSetUsername() != true){
			$e->error('astronauth.signUp.setUsernameFailed');
			return false;
		}

		if($this->account->autoSetEmail() != true){
			$e->error('astronauth.signUp.setEmailFailed');
			return false;
		}

		$password = Password::new($this->account);
		if($password->autoHash() != true){
			$e->error('astronauth.signUp.passwordHashFailed');
			return false;
		}

		if($password->insert() != true) {
			$e->error('astronauth.signUp.passwordInsertFailed');
			return false;
		}

		$this->account->password = $password;

		if($this->account->insert() != true) {
			$e->debug('astronauth.signUp.debug.account', json_encode($this->account)));
			$e->error('astronauth.signUp.accountInsertFailed');
			$password->delete();
			return false;
		} else {
			$e->info('astronauth.signUp.complete');
			return true;
		}
	}

	public function signOut() {
		global $e;
		$e->info('astronauth.signOut.begin');

		if($this->remembrance instanceof Remembrance){
			$this->remembrance->forget();
		} else {
			Remembrance::unsetCookie();
		}

		$this->signedIn = false;

		$e->info('astronauth.signOut.complete');
		return true;
	}

	public function isAuthenticated() {
		return $this->signedIn;
	}
}

$e->info('main.loaded');
?>
