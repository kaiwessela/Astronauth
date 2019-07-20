<?php
session_start();
require('config.php');
require('valid8.php');
require('account.php');
require('session.php');
require('login.php');

$pdo;

class Astronauth {
	global $pdo;
	private $account;
	private $session;
	private $login;

	private $loginState = false;

	public function __construct() {
		# connect to database
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	}

	public function init() {
		# try if there is a session
		$this->session = Session::get();
		if($this->session instanceof Session){
			# there is an existing session, proceed with request
			$this->account = Account::pull($this->sesson->getAccountUUID());
			if($this->account instanceof Account){
				$this->loggedIn(true);
				return true;
			}
		}

		# try if there is a login
		$this->login = Login::get();
		if($this->login instanceof Login){
			# there is an existing login, try to login
			if($this->login->verify()){
				# login is valid, check if account is valid
				$this->account = Account::pull($this->login->getAccountUUID());
				if($this->account instanceof Account){
					# account is valid, proceed signin
					$this->session = new Session($this->account);
					$this->session->set();
					$this->loggedIn(true);
					return true;
				}
			}
		}

		# there is no existing login, user has to log in with password
		return false;
	}

	public function signin($user, $password, $keepLoggedIn = false) {
		if(Valid8::email($user)){
			$this->account = Account::pull($user, 'email');
		} else {
			$this->account = Account::pull($user, 'username');
		}

		if($this->account instanceof Account){
			if($this->account->verify($password)){
				$this->session = new Session($this->account);
				$this->session->set();

				if($keepLoggedIn == true){
					$this->login = new Login($this->account);
					$this->login->set();
					$this->login->push();
				}

				return true;
			}
		}

		return false;
	}

	public function loggedIn($set) {
		if($set == false){
			$this->loginState = false;
		} else if($set == true){
			$this->loginState = true;
		} else {
			return $this->loginState;
		}
	}

	public function getAccountUUID() {
		if($this->loggedIn()){
			return $account->getUUID();
		} else return false;
	}

	public function getAccountUsername() {
		if($this->loggedIn()){
			return $account->getUsername();
		} else return false;
	}

	public function getAccountEmail() {
		if($this->loggedIn()){
			return $account->getEmail();
		} else return false;
	}
}
?>
