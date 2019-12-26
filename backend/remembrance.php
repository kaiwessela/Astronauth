<?php
class Remembrance {
	public $uid;
	public $accountUID;
	public $tokenHash;
	public $timestamp;
	public $period;

	private $token;


	public static function new($account, $period = 0) {
		$obj = new Remembrance();

		$obj->generateUID();
		$obj->setAccount($account);
		$obj->generateToken();
		$obj->hashToken();
		$obj->setPeriod($period);

		return $obj;
	}

	public static function autoPull() {
		if(isset($_COOKIE[COOKIE_REMEMBERME_UID]){
			return self::pull($_COOKIE[COOKIE_REMEMBERME_UID]);
		} else {
			return false;
		}
	}

	public static function pull($uid) {
		$query = 'SELECT * FROM remembrances WHERE remembrance_uid = :uid';
		$values = ['uid' => $uid];

		$s = $pdo->prepare($query);
		$s->execute($values);
		$data = $s->fetch();

		$obj = new Remembrance();
		$obj->uid = $data['remembrance_uid'];
		$obj->accountUID = $data['remembrance_account_uid'];
		$obj->tokenHash = $data['remembrance_tokenhash'];
		$obj->timestamp = $data['remembrance_timestamp'];
		$obj->period = $data['remembrance_period']

		return $obj;
	}

	public function insert() {
		$query = '	INSERT INTO remembrances
					(remembrance_uid, remebrance_account_uid, remembrance_type,
					remembrance_tokenhash, remembrance_timestamp, remembrance_period)
					VALUES :uid, :account_uid, :type, :tokenhash, :timestamp, :period		';

		$values = [
			'uid' => $this->uid,
			'account_uid' => $this->accountUID,
			'type' => $this->type,
			'tokenhash' => $this->tokenHash,
			'timestamp' => $this->timestamp,
			'period' => $this->period
		];

		$s = $pdo->prepare($query);
		return $s->execute($values);
	}

	public function update() {
		$query = '	UPDATE remembrances
					SET remembrance_tokenhash = :tokenhash, remembrance_timestamp = :timestamp,
					remembrance_period = :period
					WHERE remembrance_uid = :uid											';

		$values = [
			'tokenhash' => $this->tokenHash,
			'timestamp' => $this->timestamp,
			'period' => $this->period;
			'uid' => $this->uid
		];

		$s = $pdo->prepare($query);
		return $s->execute($values);
	}

	public function delete() {
		$query = 'DELETE FROM remembrances WHERE remembrance_uid = :uid';
		$values = ['uid' => $this->uid];

		$s = $pdo->prepare($query);
		return $s->execute($values);
	}

	public function refresh() {
		$this->setPeriod();
		$this->refreshToken();
		$this->setCookie();
		$this->update();
		return true;
	}

	public function forget() {
		$this->unsetCookie();
		$this->delete();
		return true;
	}

	public function setCookie() {
		if($this->period == 0){
			$expires = 0;
		} else {
			$expires = $this->timeout;
		}

		setcookie(COOKIE_REMEMBERME_UID, $this->uid, $expires);
		setcookie(COOKIE_REMEMBERME_TOKEN, $this->token, $expires);
	}

	public function unsetCookie() {
		self::unsetCookie();
	}

	public static function unsetCookie() {
		setcookie(COOKIE_REMEMBERME_UID, '', time() - 1);
		setcookie(COOKIE_REMEMBERME_TOKEN, '', time() - 1);
	}

	public function verify($token) {
		if($this->calcTimeout() < time()){
			return false;
		}

		return $this->verifyToken();
	}

	protected function setAccountUID($account) {
		if($account instanceof Account){
			$this->accountUID = $account->uid;
			return true;
		} else {
			return false;
		}
	}

	protected function setPeriod($period = null) {
		$this->timestamp = time();

		// IDEA: this is not intuitive (remember the value needed for a session cookie)

		if($period == null){
			$this->period = REMEMBERME_PERIOD_SESSION;
		} else if(is_int($period)){
			$this->period = $period;
		} else {
			$this->period = REMEMBERME_PERIOD_PERSISTENT;
		}
	}

	public function calcTimeout() {
		return $this->timestamp + $period;
	}

	private function generateUID() {
		$this->uid = generateUID();
		return $this->uid;
	}

	private function generateToken() {
		$this->token = bin2hex(random_bytes(64));
		return true;
	}

	public function hashToken() {
		$this->tokenHash = password_hash($this->token, PASSWORD_DEFAULT);
		return $this->tokenHash;
	}

	public function verifyToken() {
		return password_verify($token, $this->tokenHash);
	}

	public function refreshToken() {
		$this->generateToken();
		$this->hashToken();
		return true;
	}
}
?>
