<?php
require('config.php');
require('valid8.php');
require('error.php');

class Astronauth {
	public $pdo;

	public function __construct() {
		# connect to database
		$this->pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	}

	public function signin($user, $password, $keepLoggedIn) {
		$s = $this->pdo->prepare('SELECT * FROM accounts WHERE username = :user OR email = :user');
		$s->execute(array('user' => $user));
		if($s->rowCount() == 0){
			return false;
		} else {
			$r = $s->fetch();
		}

		if(password_verify($password, $r['pwhash'])){
			$_SESSION['astro_uuid'] = $r['uuid'];
			$_SESSION['astro_login'] = true;
			return true;
		} else {
			return false;
		}
	}

	public function signup($username, $email, $password, $pwcheck) {
		# validate username, email and password
		if(Valid8::username($username) && Valid8::email($email)
			&& Valid8::password($password, $pwcheck, false)){

			$uuid = uniqid();
			$pwhash = password_hash($password, PASSWORD_DEFAULT);

			$s = $this->pdo->prepare('INSERT INTO accounts (uuid, username, email, pwhash)
				VALUES (:uuid, :username, :email, :pwhash)');
			$s->execute(array('uuid' => $uuid, 'username' => $username, 'email' => $email,
				'pwhash' => $pwhash));

			return true;
		} else {
			return false;
		}
	}

	public function signoff() {

	}

	public function signout() {

	}
}
?>
