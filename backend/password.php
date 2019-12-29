<?php
class Password {
	public $uid;
	public $accountUID;
	private $hash;
	private $salt;


	public static function new($account) {
		$obj = new Password();

		$obj->generateUID();
		$obj->accountUID = $account->uid;
		$obj->generateSalt();

		return $obj;
	}

	public static function pull($uid) {
		global $pdo;

		$query = 'SELECT * FROM passwords WHERE password_uid = :uid';
		$values = ['uid' => $uid];

		$s = $pdo->prepare($query);
		$s->execute($values);
		$data = $s->fetch();

		return self::load($data);
	}

	public static function load($data) {
		$password = new Password();

		$password->uid = $data['password_uid'];
		$password->accountUID = $data['password_account_uid'];
		$password->hash = $data['password_hash'];
		$password->salt = $data['password_salt'];

		return $password;
	}

	public function autoHash() {
		return $this->hash($_POST[POST_ACCOUNT_PASSWORD]);
	}

	public function hash($password) {
		if(!is_string($password)){
			return false;
		} else if(strlen($password) < PASSWORD_MIN_LENGTH || strlen($password) > 100){
			return false;
		} else {
			$this->hash = password_hash(GLOBAL_SALT . $password . $this->salt, PASSWORD_DEFAULT);
			return true;
		}
	}

	public function insert() {
		global $pdo;

		$query = '	INSERT INTO passwords (password_uid, password_account_uid, password_hash, password_salt)
					VALUES (:uid, :account_uid, :hash, :salt)													';
		$values = [
			'uid' => $this->uid,
			'account_uid' => $this->accountUID,
			'hash' => $this->hash,
			'salt' => $this->salt
		];

		$s = $pdo->prepare($query);
		return $s->execute($values);
	}

	public function update() {
		global $pdo;

	}

	public function delete() {
		global $pdo;

	}

	public function autoVerify() {
		return $this->verify($_POST[POST_ACCOUNT_PASSWORD]);
	}

	public function verify($password) {
		return password_verify(GLOBAL_SALT . $password . $this->salt, $this->hash);
	}

	public function generateSalt() {
		$this->salt = bin2hex(random_bytes(8));
		return $this->salt;
	}

	public function generateUID() {
		$this->uid = bin2hex(random_bytes(16));
		return $this->uid;
	}
}
?>
