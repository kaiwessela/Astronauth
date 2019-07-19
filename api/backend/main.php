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
	public $account;
	public $session;
	public $login;

	public function __construct() {
		# connect to database
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	}

	public function start() {
		$this->session = Session::get();
		if(!$this->session instanceof Session){
			# there is no existing session, try if there is a login
			$this->login = Login::get();
			if(!$this->login instanceof Login){
				# there is no existing login, user has to log in with password
				# hier weiter
			} else {
				# there is an existing login, try to login
				if($this->login->verify()){
					# login is valid, write session
					$this->account = Account::pull($this->login->getAccountUUID());
					if($this->account instanceof Account){
						$this->session = new Session($this->account);
						$session->set();
					}
				}
			}
		} else {
			# there is an existing session, proceed with request
		}
	}
}
?>
