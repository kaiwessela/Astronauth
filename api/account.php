<?php
# CLASS > Account
# This is the account class. Account explains itself.

class Account {
	private $uuid; # uuid of the account, randomly generated
	private $username; # username of the account / user
	private $email; # email of the account / user
	private $pwhash; # hashed password of the account

	private $exists = false; # true = account exists on database / false = account doesnt exist on database

	//global $pdo; # reference to database connection connection

	public function __construct($uuid, $username, $email, $pwhash) {
		$this->uuid = $uuid;
		$this->username = $username;
		$this->email = $email;
		$this->pwhash = $pwhash;
	}

	# get account from db and return it
	public static function pull($value, $key = 'uuid') {
		if($key == 'uuid'){ # get account by uuid (default)
			$s = $pdo->prepare('SELECT * FROM accounts WHERE uuid = ?');
		} else if($key == 'username'){ # get account by username
			$s = $pdo->prepare('SELECT * FROM accounts WHERE username = ?');
		} else if($key == 'email'){ # get account by email
			$s = $pdo->prepare('SELECT * FROM accounts WHERE email = ?');
		}

		$s->execute(array($value));

		if($s->rowCount() == 1){ # check if there is a result from the database request
			# there is one, create account and return it
			$r = $s->fetch();
			$acc = new Account($r['uuid'], $r['username'], $r['email'], $r['pwhash']);
			$acc->exists(true);
			return $acc;
		} else {
			# there is no result
			return false;
		}
	}

	# create new account and return it - only use if account doesnt exist already - generates some values
	public static function new($username, $email, $password) {
		return new Account(self::generateUUID(), $username, $email, self::generateHash($password));
	}

	# check if a password is correct for the account
	public function verify($password) {
		return password_verify($password, $this->pwhash);
	}

	# push account to database, update or insert
	public function push() {
		if($this->exists()){
			$s = $pdo->prepare('UPDATE accounts SET username = :username, email = :email, pwhash = :pwhash WHERE uuid = :uuid');
		} else {
			$s = $pdo->prepare('INSERT INTO accounts (uuid, username, email, pwhash) VALUES (:uuid, :username, :email, :pwhash)');
		}

		$s->execute(array('uuid' => $this->uuid, 'username' => $this->username, 'email' => $this->email, 'pwhash' => $this->pwhash));
	}

	# check if account exists or set this value
	private function exists($set) {
		if($set == true){
			$this->exists = true;
		} else if($set == false){
			$this->exists = false;
		} else {
			return $this->exists;
		}
	}

	# generate an uuid
	public static function generateUUID() {
		$hex = bin2hex(random_bytes(16)); # generate 16 random bytes and convert them to a hexadecimal string
		$chu = explode(' ', chunk_split($hex, 4, ' ')); # split that string to 4 chars long chunks
		return $chu[0].$chu[1].'-'.$chu[2].'-'.$chu[3].'-'.$chu[4].'-'.$chu[5].$chu[6].$chu[7]; # return and set -
	}

	# generate a hash of a given password
	public static function generateHash($password) {
		return password_hash($password, PASSWORD_DEFAULT);
	}

	public function getUUID() {
		return $this->uuid;
	}

	public function getUsername() {
		return $this->username;
	}

	public function getEmail() {
		return $this->email;
	}
}
?>
