<?php
namespace Astronauth\Backend\Classes;
use PDO;
use \Astronauth\Backend\Classes\Exceptions\DatabaseException;
use \Astronauth\Backend\Classes\Exceptions\InvalidInputException;
use \Astronauth\Backend\Classes\Exceptions\ObjectAlreadyExistsException;
use \Astronauth\Backend\Classes\Exceptions\ObjectNotEmptyException;
use \Astronauth\Backend\Classes\Exceptions\ObjectNotFoundException;

class Account {
	public $id;
	public $name;
	public $email;
	public $pwhash;

	private $pdo;
	private $new;
	private $empty;


	function __construct(PDO &$pdo) {
		$this->pdo = &$pdo;
		$this->empty = true;
	}

	public function generate() {
		if(!$this->empty){
			throw new ObjectNotEmptyException($this);
		}

		$this->generate_id();

		$this->new = true;
		$this->empty = false;
	}

	public function pull(string $identifier){
		if(!$this->empty){
			throw new ObjectNotEmptyException($this);
		}

		$query = 'SELECT * FROM accounts WHERE account_name = :identifier OR account_email = :identifier';
		$values = ['identifier' => $identifier];

		$s = $this->pdo->prepare($query);
		if(!$s->execute($values)){
			throw new DatabaseException($s);
		} else if($s->rowCount() != 1){
			throw new \Exception('not able to pull account');
		} else {
			$this->load($s->fetchObject());
		}
	}

	public function pull_by_id(string $id) {
		if(!$this->empty){
			throw new ObjectNotEmptyException($this);
		}

		$query = 'SELECT * FROM accounts WHERE account_id = :id';
		$values = ['id' => $id];

		$s = $this->pdo->prepare($query);
		if(!$s->execute($values)){
			throw new DatabaseException($s);
		} else if($s->rowCount() != 1){
			throw new \Exception('not able to pull account');
		} else {
			$this->load($s->fetchObject());
		}
	}

	public function pull_by_id_or_email($id, $email) {
		if(!$this->empty){
			throw new ObjectNotEmptyException($this);
		}

		$query = 'SELECT DISTINCT * FROM accounts WHERE account_id = :id OR account_email = :email';
		$values = ['id' => $id, 'email' => $email];

		$s = $this->pdo->prepare($query);
		if(!$s->execute($values)){
			throw new DatabaseException($s);
		} else if($s->rowCount() != 1){
			throw new ObjectNotFoundException();
		} else {
			$this->load($s->fetchObject());
		}
	}

	public function load($data){
		if(!$this->empty){
			throw new ObjectNotEmptyException($this);
		}

		$this->id = $data->account_id;
		$this->name = $data->account_name;
		$this->email = $data->account_email;
		$this->pwhash = $data->account_pwhash;

		$this->empty = false;
		$this->new = false;
	}

	public function push() {
		if($this->empty){
			throw new Exception(); // TODO
		}

		if($this->new){
			$query = <<<SQL
INSERT INTO accounts (account_id, account_name, account_email, account_pwhash)
VALUES (:id, :name, :email, :pwhash)
SQL;

			$values = [
				'id' => $this->id,
				'name' => $this->name,
				'email' => $this->email,
				'pwhash' => $this->pwhash
			];
		} else {
			$query = <<<SQL
SQL;

			$values = [

			];
		}

		$s = $this->pdo->prepare($query);
		if($s->execute($values)){

		} else {

		}
	}

	public function insert($data) {
		if(!isset($data['name'])){
			throw new InvalidInputException('name', '[A-Za-z0-9._-]{4,32}');
		} else if(!preg_match('/^[A-Za-z0-9._-]{4,32}$/', $data['name'])){
			throw new InvalidInputException('name', '[A-Za-z0-9._-]{4,32}', $data['name']);
		} else {
			$this->name = $data['name'];
		}

		if(!isset($data['email'])){
			throw new InvalidInputException('email', '');
		} else if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
			throw new InvalidInputException('email', '', $data['email']);
		} else {
			$this->email = $data['email'];
		}

		try {
			$account = new Account();
			$account->pull_by_id_or_email($this->id, $this->email);
			$found = true;
		} catch(ObjectNotFoundException $e){
			$found = false;
		}

		if($found){
			throw new ObjectAlreadyExistsException();
		}

		if(!isset($data['password'])){
			throw new InvalidInputException('password', '.{8,128}');
		} else if(!preg_match('/^.{8,128}$/', $data['password'])){
			throw new InvalidInputException('password', '.{8,128}', 'N/A');
		} else {
			$this->hash_password($data['password']);
		}

		$this->push();
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

	public function goodnight() {
		unset($this->pdo);
	}

	public function goodmorning(PDO &$pdo) {
		$this->pdo = $pdo;
	}

	/*public function hibernate() {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'email' => $this->email,
			'pwhash' => $this->pwhash,
			'new' => $this->new,
			'empty' => $this->empty
		]
	}

	public function wakeup(PDO &$pdo, $data) {
		$this->pdo = &$pdo;
		$this->id = $data['id'];
		$this->name = $data['name'];
		$this->email = $data['email'];
		$this->pwhash = $data['pwhash'],
		$this->new = $data['new'],
		$this->empty = $data['empty'];
	}*/
}
?>
