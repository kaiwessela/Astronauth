<?php
namespace Astronauth\Model\DatabaseObjects;
use \Astronauth\Config\Config;
use \Astronauth\Model\DatabaseObject;
use \Astronauth\Model\DatabaseObjects\Account;
use \Astronauth\Exceptions\DatabaseException;
use \Astronauth\Exceptions\EmptyResultException;
use \Astronauth\Exceptions\WrongObjectStateException;

class Device extends DatabaseObject {
	public $tokenhash;
	public $timestamp;
	public $active;
	public $account;

	/* @inherited
	public $id;

	private $new;
	private $empty;
	*/


	public function create(Account $account) {
		parent::generate();

		$this->timestamp = time();
		$this->active = true;
		$this->account = $account;
		return $this->generate_token();
	}

	public function pull($id) {
		$pdo = self::open_pdo();

		if(!$this->is_empty()){
			throw new WrongObjectStateException('empty');
		}

		$query = 'SELECT * FROM devices LEFT JOIN accounts ON account_id = device_account_id WHERE device_id = :id';
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

	public function push() {
		$pdo = self::open_pdo();

		if($this->is_empty()){
			throw new WrongObjectStateException('not empty');
		}

		$values = [
			'id' => $this->id,
			'tokenhash' => $this->tokenhash,
			'timestamp' => $this->timestamp,
			'active' => (int) $this->active
		];

		if($this->is_new()){
			$query = 'INSERT INTO devices (device_id, device_tokenhash, device_timestamp, device_active, device_account_id)
					VALUES (:id, :tokenhash, :timestamp, :active, :account_id)';

			$values['account_id'] = $this->account->id;
		} else {
			$query = 'UPDATE devices SET device_tokenhash = :tokenhash, device_timestamp = :timestamp, device_active = :active
					WHERE device_id = :id';
		}

		$s = $pdo->prepare($query);
		if(!$s->execute($values)){
			throw new DatabaseException($s);
		}
	}

	public function load($data) {
		if(!$this->is_empty()){
			throw new WrongObjectStateException('empty');
		}

		$this->id = $data['device_id'];
		$this->tokenhash = $data['device_tokenhash'];
		$this->timestamp = (int) $data['device_timestamp'];
		$this->active = (bool) $data['device_active'];
		$this->account = new Account();
		$this->account->load($data);

		$this->empty = false;
		$this->new = false;
	}

	public function verify($token) {
		if($this->is_new()){
			throw new WrongObjectStateException('not new');
		}

		if($this->is_empty()){
			throw new WrongObjectStateException('not empty');
		}

		if($this->timestamp + 3600 * 24 * Config::COOKIE_ENDURANCE < time()){
			return false;
		}

		return password_verify($token, $this->tokenhash);
	}

	public function refresh() {
		if($this->is_new()){
			throw new WrongObjectStateException('not new');
		}

		if($this->is_empty()){
			throw new WrongObjectStateException('not empty');
		}

		$token = $this->generate_token();
		$this->push();

		return $token;
	}

	public function deactivate() {
		if($this->is_empty()){
			throw new WrongObjectStateException('not empty');
		}

		$this->active = false;
		$this->push();
	}

	private function generate_token() {
		$token = bin2hex(openssl_random_pseudo_bytes(64));
		$this->tokenhash = password_hash($token, PASSWORD_DEFAULT);

		return $token;
	}


}
?>
