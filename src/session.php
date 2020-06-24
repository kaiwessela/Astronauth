<?php
class Session {
	public $key;
	public $token_hash;
	private $token_plain;
	public $setdate;
	public $durable;

	const EXPIRATION_TIME = 180 * 24 * 60 * 60;


	public static function generate($durable = false) {
		$obj = new Session();
		$obj->generate_key();
		$obj->generate_token();
		$obj->setdate = time();

		if($durable){
			$obj->durable = true;
		} else {
			$obj->durable = false;
		}

		return $obj;
	}

	public static function read() {
		if(isset($_SESSION['astronauth_key'])){
			return self::pull($_SESSION['astronauth_key']);
		} else if(isset($_COOKIE['astronauth_key'])){
			return self::pull($_COOKIE['astronauth_key']);
		} else {
			// error or false
		}
	}

	public static function pull($key) {
		global $pdo;

		$query = 'SELECT * FROM sessions WHERE session_key = :key';
		$values = ['key' => $key];

		$s = $pdo->prepare($query);
		if($s->execute($values) == false){
			// error
		} else if($s->rowCount() != 1){
			// error
		} else {
			return Session::load($s->fetchObject());
		}
	}

	public static function load($data) {
		$obj = new Session();
		$obj->key = $data->session_key;
		$obj->token_hash = $data->session_tokenhash;
		$obj->setdate = (int) $data->session_setdate;
		$obj->durable = (boolean) $data->session_durability;
		return $obj;
	}

	public function push() {
		$this->hash_token();

		$query = '';
	}

	public function write() {

	}

	public function refresh() {
		if(!$this->is_durable()){
			// error
		}

		$this->generate_token();
		$this->hash_token();

		$this->update();
		$this->set_cookies();
	}

	public function archive() {

	}

	public function set_cookies() {
		setcookie('astronauth_key', $this->key, time() + self::EXPIRATION_TIME);
		setcookie('astronauth_token', $this->token_plain, time() + self::EXPIRATION_TIME);
	}

	public function unset_cookies() {
		setcookie('astronauth_key', '', time() - 1);
		setcookie('astronauth_token', '', time() - 1);
	}

	public function is_durable() {
		return $this->durable;
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
