<?php
class Password extends AuthenticationFactor {
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
		$password->account = $data['password_account'];
		$password->hash = $data['password_hash'];
		$password->salt = $data['password_salt'];

		return $password;
	}

	public function hash($password) {
		if(strlen($password) < PASSWORD_MIN_LENGTH || strlen($password) > 100){
			return false;
		} else {
			$this->hash = password_hash(GLOBAL_SALT . $password . $this->salt, PASSWORD_DEFAULT);
			return true;
		}
	}

	public function insert() {

	}

	public function update() {

	}

	public function delete() {

	}

	public function verify($password) {
		$pw = $password or $_POST[POST_ACCOUNT_PASSWORD];
		return password_verify(GLOBAL_SALT . $pw . $this->salt, $this->hash);
	}

	public function generateSalt() {
		$this->salt = bin2hex(random_bytes(8));
		return $this->salt;
	}

	public function generateUID() {
		$this->uid = generateUID();
		return $this->uid;
	}
}
?>
