<?php
class Account {
	public $uid;
	public $username;
	public $email;
	public $password;
	public $remebrances = [];

	public static function new() {
		$account = new Account();

		$account->generateUID();

		return $account;
	}

	public static function autoPull() {
		return self::pull($_POST[POST_ACCOUNT_IDENTIFIER]);
	}

	public static function pull($identifier) {
		global $pdo;

		$query = '	SELECT * FROM accounts
					LEFT JOIN passwords ON password_account_uid = account_uid
					LEFT JOIN remembrances ON remembrance_account_uid = account_uid
					WHERE account_username = :identifier OR account_email = :identifier		';

		$values = ['identifier' => $identifier];

		$s = $pdo->prepare($query);
		if($s->execute($values) == false){
			return false;
		}

		$obj = new Account();

		$i = 0;
		while($r = $s->fetch()){
			if($i == 0){
				$obj->uid = $r['account_uid'];
				$obj->username = $r['account_username'];
				$obj->email = $r['account_email'];
				$obj->password = Password::load($r);
			}

			$obj->remembrances[$i] = Remembrance::load($r);

			$i++;
		}

		return $obj;
	}

	public function insert() {
		global $pdo;

		$query = 'INSERT INTO accounts (account_uid, account_username, account_email) VALUES (:uid, :username, :email)';
		$values = ['uid' => $this->uid, 'username' => $this->username, 'email' => $this->email];

		$s = $pdo->prepare($query);
		return $s->execute($values);
	}

	public function update() {
		global $pdo;

		$query = 'UPDATE account SET account_username = :username, account_email = :email WHERE account_uid = :uid';
		$values = ['username' => $this->username, 'email' => $this->email, 'uid' => $this->uid];

		$s = $pdo->prepare($query);
		return $s->execute($values);
	}

	public function delete() {
		global $pdo;

		$query = 'DELETE FROM accounts WHERE account_uid = :uid';
		$values = ['uid' => $this->uid];

		$s = $pdo->prepare($query);
		return $s->execute($values);
	}

	public function autoSetUsername() {
		return $this->setUsername($_POST[POST_ACCOUNT_USERNAME]);
	}

	public function setUsername($username) {
		if(!is_string($username)){
			return false;
		} else if(strlen($username) < 3 || strlen($username) > 30 || !preg_match('/^[a-zA-Z0-9._]*$/', $username)){
			return false;
		} else {
			$this->username = $username;
			return true;
		}
	}

	public function autoSetEmail() {
		return $this->setEmail($_POST[POST_ACCOUNT_EMAIL]);
	}

	public function setEmail($email) {
		if(!is_string($email)){
			return false;
		} else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			return false;
		} else {
			$this->email = $email;
			return true;
		}
	}

	public function generateUID() {
		$this->uid = bin2hex(random_bytes(16));
		return $this->uid;
	}
}
?>
