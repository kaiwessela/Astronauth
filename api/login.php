<?php
class Login {
	private $account_uuid;
	private $identifier;
	private $token;
	private $timestamp;

	private $exists = false;

	global $pdo;

	public function __construct($account) {
		$this->account_uuid = $account->getUUID();
	}

	public static function get() {
		if(isset($_COOKIE['astronauth_login'])){
			$login = unserialize($_COOKIE['astronauth_login']);
		} else return false;

		if($login instanceof Login){
			$login->exists(true);
			return $login;
		} else {
			return false;
		}
	}

	public function verify() {
		$s = $pdo->prepare('SELECT * FROM login WHERE identifier = ?');
		$s->execute(array($this->identifier));

		if($s->rowCount() != 1){
			return false;
		} else {
			$r = $s->fetch();
		}

		if($timestamp + (6 * 30 * 24 * 60 * 60) < time()){
			return false;
		}

		if(password_verify($token, $r['token'])){
			return true;
		} else {
			return false;
		}
	}

	public function set() {
		if(!$this->exists()){
			$this->identifier = generateIdentifier();
		}

		$this->token = generateToken();
		$this->timestamp = time();

		setcookie('astronauth_login', serialize($this), time() + (12 * 30 * 24 * 60 * 60));

		return true;
	}

	public function unset() {
		setcookie('astronauth_login', 'deleted', time() - 3600);
		$s = $pdo->prepare('DELETE FROM logins WHERE identifier = ?');
		$s->execute(array($this->identifier));
		$this->exists(false);
		return true;
	}

	public function push() {
		if($this->exists()){
			$s = $pdo->prepare('UPDATE logins SET token = :token, timestamp = :timestamp
				WHERE identifier = :identifier');
		} else {
			$s = $pdo->prepare('INSERT INTO logins (account_uuid, identifier, token, timestamp)
				VALUES (:account_uuid, :identifier, :token, :timestamp)');
		}

		$s->execute(array('account_uuid' => $this->account_uuid, 'identifier' => $this->identifier,
			'token' => password_hash($this->token, PASSWORD_DEFAULT), 'timestamp' => $this->timestamp));

		return true;
	}

	public function exists($set) {
		if($set == true){
			$this->exists = true;
		} else if($set == false){
			$this->exists = false;
		} else {
			return $this->exists;
		}
	}

	private function generateIdentifier() {
		return bin2hex(random_bytes(16));
	}

	private function generateToken() {
		return bin2hex(random_bytes(32));
	}

	public function getAccountUUID() {
		return $this->account_uuid;
	}
}
?>
