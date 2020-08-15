<?php
namespace Astronauth\Backend\Classes;
use PDO;
use Astronauth\Backend\Classes\Exceptions\DatabaseException;
use Astronauth\Backend\Classes\Exceptions\ObjectNotEmptyException;
use Astronauth\Backend\Classes\Exceptions\ObjectNotFoundException;

class Device {
	public $key;
	public $token_hash;
	public $timestamp;
	public $active;
	public $account_id;

	private $token_plain;

	private $pdo;
	private $new;
	private $empty;

	const EXPIRATION_TIME = 180 * 24 * 60 * 60;


	function __construct(PDO &$pdo) {
		$this->pdo = &$pdo;
		$this->new = false;
		$this->empty = true;
	}

	public function generate($account_id) {
		if(!$this->empty){
			throw new ObjectNotEmptyException($this);
		}

		$this->generate_key();
		$this->generate_token();
		$this->timestamp = time();
		$this->active = true;
		$this->account_id = $account_id;

		$this->new = true;
		$this->empty = false;
	}

	public function read() {
		if(!$this->empty){
			throw new ObjectNotEmptyException($this);
		}

		if(isset($_COOKIE['astronauth_key'])){
			$this->pull($_COOKIE['astronauth_key'] ?? null);
		}
	}

	public function pull($key) {
		if(!$this->empty){
			throw new ObjectNotEmptyException($this);
		}

		$query = 'SELECT * FROM devices WHERE device_key = :key';
		$values = ['key' => $key];

		$s = $this->pdo->prepare($query);
		if(!$s->execute($values)){
			throw new DatabaseException($s);
		} else if($s->rowCount() != 1){
			throw new ObjectNotFoundException();
		} else {
			$this->load($s->fetchObject());
		}
	}

	public function load($data) {
		if(!$this->empty){
			throw new ObjectNotEmptyException($this);
		}

		$this->key = $data->device_key;
		$this->token_hash = $data->device_tokenhash;
		$this->timestamp = (int) $data->device_timestamp;
		$this->active = (bool) $data->device_active;
		$this->account_id = $data->device_account_id;

		$this->new = false;
		$this->empty = false;
	}

	public function push() {
		if($this->empty){
			throw new ObjectEmptyException();
		}

		if($this->new){
			$this->hash_token();

			$query = <<<SQL
INSERT INTO devices (device_key, device_tokenhash, device_timestamp, device_active, device_account_id)
VALUES (:key, :tokenhash, :timestamp, :active, :account_id)
SQL;

			$values = [
				'key' => $this->key,
				'tokenhash' => $this->token_hash,
				'timestamp' => $this->timestamp,
				'active' => $this->active,
				'account_id' => $this->account_id
			];
		} else {
			$query = 'UPDATE devices SET device_tokenhash = :tokenhash, device_active = :active WHERE device_key = :key';
			$values = [
				'key' => $this->key,
				'tokenhash' => $this->token_hash,
				'active' => (int) $this->active
			];
		}

		$s = $this->pdo->prepare($query);
		if(!$s->execute($values)){
			throw new DatabaseException($s);
		} else {
			$this->new = false;
		}
	}

	public function refresh() {
		$this->generate_token();
		$this->hash_token();

		$this->write();
		$this->push();
	}

	public function archive() {
		$this->active = false;
		$this->erase();
		$this->push();
	}

	public function verify() {
		return ($this->verify_token() && $this->is_active());
	}

	public function write() {
		setcookie('astronauth_key', $this->key, time() + self::EXPIRATION_TIME);
		setcookie('astronauth_token', $this->token_plain, time() + self::EXPIRATION_TIME);
	}

	public function erase() {
		setcookie('astronauth_key', '', 1);
		setcookie('astronauth_token', '', 1);
	}

	private function generate_key() {
		$this->key = bin2hex(openssl_random_pseudo_bytes(32));
	}

	private function generate_token() {
		$this->token_plain = bin2hex(openssl_random_pseudo_bytes(32));
	}

	private function hash_token() {
		$this->token_hash = password_hash($this->token_plain, PASSWORD_DEFAULT);
	}

	private function verify_token() {
		return password_verify($_COOKIE['astronauth_token'] ?? null, $this->token_hash);
	}

	private function is_active() {
		return (bool) $this->active;
	}

	public function is_new() {
		return $this->new;
	}

	public function is_empty() {
		return $this->empty;
	}

	public function goodnight() {
		unset($this->token_plain);
		unset($this->pdo);
	}

	public function goodmorning(PDO &$pdo) {
		$this->pdo = $pdo;
	}

	/*
	public function hibernate() {
		return [
			'key' => $this->key,
			'token_hash' => $this->token_hash,
			'timestamp' => $this->timestamp,
			'active' => $this->active,
			'new' => $this->new,
			'empty' => $this->empty
		]
	}

	public function wakeup(PDO &$pdo, $data) {
		$this->pdo = &$pdo;
		$this->key = $data['key'];
		$this->token_hash = $data['token_hash'];
		$this->timestamp = $data['timestamp'];
		$this->active = $data['active'],
		$this->new = $data['new'],
		$this->empty = $data['empty'];
	}
	*/
}
?>
