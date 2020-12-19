<?php
namespace Astronauth\Model\DatabaseObjects;
use \Astronauth\Model\DatabaseObject;
use \Astronauth\Exceptions\DatabaseException;
use \Astronauth\Exceptions\EmptyResultException;
use \Astronauth\Exceptions\WrongObjectStateException;

class Account extends DatabaseObject {
	public $name;
	public $email;
	public $pwhash;

	/* @inherited
	public $id;

	private $new;
	private $empty;
	*/


	public function pull($id) {
		$pdo = self::open_pdo();

		if(!$this->is_empty()){
			throw new WrongObjectStateException('empty');
		}

		$query = 'SELECT * FROM accounts WHERE account_id = :id';
		$values = ['id' => $id];

		$s = $pdo->prepare($query);
		if(!$s->execute($values)){
			throw new DatabaseException($s);
		} else if($s->rowCount() != 1){
			throw new EmptyResultException($query, $values);
		} else {
			$this->load($s->fetch());
		}
	}

	public function pull_by_name_or_email($identifier) {
		$pdo = self::open_pdo();

		if(!$this->is_empty()){
			throw new WrongObjectStateException('empty');
		}

		$query = 'SELECT * FROM accounts WHERE account_name = :identifier OR account_email = :identifier';
		$values = ['identifier' => $identifier];

		$s = $pdo->prepare($query);
		if(!$s->execute($values)){
			throw new DatabaseException($s);
		} else if($s->rowCount() != 1){
			throw new EmptyResultException($query, $values);
		} else {
			$this->load($s->fetch());
		}
	}

	public function push() {
		$pdo = self::open_pdo();

		if($this->is_empty()){
			throw new WrongObjectStateException('not empty');
		}

		$values = [
			'id' => $this->id,
			'name' => $this->name,
			'email' => $this->email,
			'pwhash' => $this->pwhash
		];

		if($this->is_new()){
			$query = 'INSERT INTO accounts (account_id, account_name, account_email, account_pwhash)
					VALUES (:id, :name, :email, :pwhash)';
		} else {
			$query = 'UPDATE accounts SET account_name = :name, account_email = :email, account_pwhash = :pwhash WHERE account_id = :id';
		}

		$s = $pdo->prepare($query);
		if(!$s->execute($values)){
			throw new DatabaseException($s);
		}
	}

	public function load($data){
		if(!$this->is_empty()){
			throw new WrongObjectStateException('empty');
		}

		$this->id = $data['account_id'];
		$this->name = $data['account_name'];
		$this->email = $data['account_email'];
		$this->pwhash = $data['account_pwhash'];

		$this->empty = false;
		$this->new = false;
	}

	public function set_name($name) {
		if(!$this->is_new()){
			throw new WrongObjectStateException('new');
		}
	}

	public function set_email($email) {

	}

	public function set_password($password) {
		if(strlen($password) < 8){

		}
	}

	public function verify_password($password) {
		if($this->is_new()){
			throw new WrongObjectStateException('not new');
		}

		if($this->is_empty()){
			throw new WrongObjectStateException('not empty');
		}

		if(!password_verify($password, $this->pwhash)){
			return false;
		}

		if(password_needs_rehash($this->pwhash)){
			try {
				$this->set_password($password);
				$this->push();
			} catch(\Exception $e){
				// do nothing because priority of rehash is low
			}
		}

		return true;
	}
}
?>
