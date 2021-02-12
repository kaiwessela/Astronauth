<?php
namespace Astronauth\Backend\Models;
use \Astronauth\Backend\ModelTrait;
use \Astronauth\Backend\Exceptions\DatabaseException;
use \Astronauth\Backend\Exceptions\InvalidInputException;
use \Astronauth\Backend\Exceptions\ObjectAlreadyExistsException;
use \Astronauth\Backend\Exceptions\WrongObjectStateException;
use \Astronauth\Backend\Exceptions\ObjectNotFoundException;
use PDO;

class Account {
	public $id;
	public $name;
	public $email;
	public $pwhash;

	private $new;
	private $empty;

	use ModelTrait;


	function __construct() {
		$this->new = false;
		$this->empty = true;
	}

	public function generate() {
		if(!$this->empty){
			throw new WrongObjectStateException('empty');
		}

		$this->generate_id();

		$this->new = true;
		$this->empty = false;
	}

	public function pull(string $identifier){
		$pdo = self::open_pdo();

		if(!$this->empty){
			throw new WrongObjectStateException('empty');
		}

		$query = 'SELECT * FROM accounts WHERE account_name = :identifier OR account_email = :identifier';
		$values = ['identifier' => $identifier];

		$s = $pdo->prepare($query);
		if(!$s->execute($values)){
			throw new DatabaseException($s);
		} else if($s->rowCount() != 1){
			throw new ObjectNotFoundException();
		} else {
			$this->load($s->fetchObject());
		}
	}

	public function pull_by_id(string $id) {
		$pdo = self::open_pdo();

		if(!$this->empty){
			throw new WrongObjectStateException('empty');
		}

		$query = 'SELECT * FROM accounts WHERE account_id = :id';
		$values = ['id' => $id];

		$s = $pdo->prepare($query);
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
			throw new WrongObjectStateException('empty');
		}

		$this->id = $data->account_id;
		$this->name = $data->account_name;
		$this->email = $data->account_email;
		$this->pwhash = $data->account_pwhash;

		$this->empty = false;
		$this->new = false;
	}

	public function push() {
		$pdo = self::open_pdo();

		if($this->empty){
			throw new WrongObjectStateException('not empty');
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

		$s = $pdo->prepare($query);
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

		$found = false;
		try {
			$account = new Account();
			$account->pull($this->id);
			$found = true;
		} catch(ObjectNotFoundException $e){}

		try {
			$account = new Account();
			$account->pull($this->email);
			$found = true;
		} catch(ObjectNotFoundException $e){}

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
}
?>
