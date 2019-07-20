<?php
class Account {
	private $uuid;
	private $username;
	private $email;
	private $pwhash;

	private $exists = false;

	global $pdo;

	public function __construct($uuid, $username, $email, $pwhash) {
		$this->uuid = $uuid;
		$this->username = $username;
		$this->email = $email;
		$this->pwhash = $pwhash;
	}

	public static function pull($value, $key = 'uuid') { // load account from database and return it
		if($key == 'uuid'){
			$s = $pdo->prepare('SELECT * FROM accounts WHERE uuid = ?');
		} else if($key == 'username'){
			$s = $pdo->prepare('SELECT * FROM accounts WHERE username = ?');
		} else if($key == 'email'){
			$s = $pdo->prepare('SELECT * FROM accounts WHERE email = ?');
		} else {
			return false;
		}

		$s->execute(array($value));

		if($s->rowCount() == 1){
			$r = $s->fetch();
			$acc = new Account($r['uuid'], $r['username'], $r['email'], $r['pwhash']);
			$acc->exists()
		} else {
			return false;
		}
	}

	public function verify($password) {
		return password_verify($password, $this->pwhash);
	}

	public function push() {

	}

	private function exists($set) { // check if account exists on database or set existance
		if($set == true){
			$this->exists = true;
		} else if($set == false){
			$this->exists = false;
		} else {
			return $this->exists;
		}
	}

	public function generateUUID() {
		$hex = bin2hex(random_bytes(16));
		$chu = explode(' ', chunk_split($hex, 4, ' '));
		return $chu[0].$chu[1].'-'.$chu[2].'-'.$chu[3].'-'.$chu[4].'-'.$chu[5].$chu[6].$chu[7];
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
