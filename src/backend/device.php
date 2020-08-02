<?php
namespace Astronauth;

class Device {
	public $key;
	public $token_hash;
	private $token_plain;
	public $timestamp;

	const EXPIRATION_TIME = 180 * 24 * 60 * 60;


	public static function generate() {
		$obj = new Device();
		$obj->generate_key();
		$obj->generate_token();
		$obj->timestamp = time();
		return $obj;
	}

	public static function read() {
		if(isset($_COOKIE['astronauth_key']) && isset($_COOKIE['astronauth_token'])){
			return self::pull($_COOKIE['astronauth_key']);
		} else {
			throw new ObjectNotFoundException();
		}
	}

	public static function pull($key) {
		global $pdo;

		$query = 'SELECT * FROM devices WHERE device_key = :key';
		$values = ['key' => $key];

		$s = $pdo->prepare($query);
		if($s->execute($values)){
			return self::load($s->fetchObject());
		} else {
			throw new DatabaseException($s);
		}
	}

	public static function load($data) {
		$obj = new self();
		$obj->key = $data->device_key;
		$obj->token_hash = $data->device_tokenhash;
		$obj->timestamp = (int) $data->device_timestamp;
		return $obj;
	}

	public function push($insert = false) {
		global $pdo;

		$this->hash_token();

		$values = [
			'key' => $this->key,
			'tokenhash' => $this->tokenhash
		];

		if($insert){
			$query = <<<SQL
INSERT INTO devices (device_key, device_tokenhash, device_timestamp)
VALUES (:key, :tokenhash, :timestamp)
SQL;

			$values['timestamp'] = $this->timestamp;
		} else {
			$query = 'UPDATE devices SET device_tokenhash = :tokenhash WHERE device_key = :key';
		}

		$s = $pdo->prepare($query);
		if($s->execute($values)){
			return true;
		} else {
			throw new DatabaseException($s);
		}
	}

	public function write() {

	}

	public function refresh() {
		$this->generate_token();
		$this->hash_token();

		$this->update();
		$this->set_cookies();
	}

	public function archive() {
		$this->unset_cookies();
	}

	public function set_cookies() {
		setcookie('astronauth_key', $this->key, time() + self::EXPIRATION_TIME);
		setcookie('astronauth_token', $this->token_plain, time() + self::EXPIRATION_TIME);
	}

	public function unset_cookies() {
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

	public function verify_token() {
		return password_verify($_COOKIE['astronauth_token'], $this->token_hash);
	}
}
?>
