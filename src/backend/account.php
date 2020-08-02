<?php
namespace Astronauth;

class Account {
	public $id;
	public $name;
	public $email;
	public $pwhash;


	function __construct() {

	}

	public static function new() {
		$obj = new self();
		$obj->generate_id();
		return $obj;
	}

	public static function pull($identifier){
		global $pdo;

		$query = 'SELECT * FROM accounts WHERE account_name = :identifier OR account_email = :identifier';
		$values = ['identifier' => $identifier];

		$s = $pdo->prepare($query);
		if(!$s->execute($values)){
			throw new DatabaseException($s);
		} else if($s->rowCount() != 1){
			throw new \Exception('not able to pull account');
		} else {
			return Account::load($s->fetchObject());
		}
	}

	public static function pull_by_id($id) {
		global $pdo;

		$query = 'SELECT * FROM accounts WHERE account_id = :id';
		$values = ['id' => $id];

		$s = $pdo->prepare($query);
		if($s->execute($values) == false){

		} else if($s->rowCount() != 1){

		} else {
			return Account::load($s->fetchObject());
		}
	}

	public static function load($data){
		$obj = new Account();
		$obj->id = $data->account_id;
		$obj->name = $data->account_name;
		$obj->email = $data->account_email;
		$obj->pwhash = $data->account_pwhash;
		return $obj;
	}

	public function insert($data) {
		global $pdo;

		if(isset($data['name'])){
			if(preg_match('/^[A-Za-z0-9.-_]{4,32}$/', $data['name'])){ // NOTE maybe the last - has to be escaped
				$this->name = $data['name'];
			} else {
				throw new \Exception('name invalid');
			}
		} else {
			throw new \Exception('no name provided');
		}

		if(isset($data['email'])){
			if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
				$this->email = $data['email'];
			} else {
				throw new \Exception('email invalid');
			}
		} else {
			throw new \Exception('no email provided');
		}

		if(isset($data['password'])){
			if(preg_match('/^.{8,128}$/', $data['password'])){
				$this->hash_password($data['password']);
			} else {
				throw new \Exception('password invalid');
			}
		} else {
			throw new \Exception('no password provided');
		}

		$query = <<<SQL
INSERT INTO accounts
(account_id, account_name, account_email, account_pwhash)
VALUES (:id, :name, :email, :pwhash)
SQL;

		$values = [
			'id' => $this->id,
			'name' => $this->name,
			'email' => $this->email,
			'pwhash' => $this->pwhash
		];

		$s = $pdo->prepare($query);
		if(!$s->execute($values)){
			throw new \Exception('database error');
		} else {
			return true;
		}
	}

	private function generate_id() {
		$this->id = bin2hex(openssl_random_pseudo_bytes(4));
	}

	private function hash_password($password) {
		$this->pwhash = password_hash($password, PASSWORD_DEFAULT);
	}

	public function verify_password($password) {
		return password_verify($password, $this->pwhash);
	}
}
?>
