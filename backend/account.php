<?php
class Account {
	private $uid;
	private $username;

	private $password;
	private $sessions = [];


	public static function new() {
		$account = new Account();

		$account->uid = generateUID();

		return $account;
	}

	public static function autoPull() {
		
	}

	public static function pull($identifier) {
		$query = '	SELECT * FROM accounts
					LEFT JOIN passwords ON password_account_uid = account_uid
					LEFT JOIN sessions ON session_account_uid = account_uid
					WHERE account_uid = :identifier OR account_username = :identifier	';

		$values = ['identifier' => $identifier];
	}

	# @param $data (Array)
	public static function load($data) {
		$account = new Account();

		$account->uuid = $data[0]['uuid'];
		$account->username = $data[0]['username'];

		$account->password = Password::load($data[0]);

		foreach($data as $d){
			$account->sessions[] = Session::load($d);
		}

		return $account;
	}

	public function commitUsername($username) {
		if(strlen($username) < 3 || strlen($username) > 30 || !preg_match('/^[a-zA-Z0-9._]*$/', $username)){
			return false;
		} else {
			$this->username = $username;
			return true;
		}
	}


}
?>
