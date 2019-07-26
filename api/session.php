<?php
class Session {
	private $account_uuid;
	private $identifier;
	private $timestamp;

	private $exists = false;

	public function __construct($account) {
		$this->account_uuid = $account->getUUID();
	}

	public static function get() {
		if(isset($_SESSION['astronauth_session'])){
			$session = unserialize($_SESSION['astronauth_session']);
		} else return false;

		if($session instanceof Session){
			$session->exists(true);
			return $session;
		} else {
			return false;
		}
	}

	public function set() {
		if(!$this->exists()){
			$this->identifier = generateIdentifier();
		}

		$this->timestamp = time();

		$_SESSION['astronauth_session'] = serialize($this);
	}

	public function unset() {
		unset($_SESSION['astronauth_session']);
		$this->exists(false);
		return true;
	}

	public function exists($set) {
		if($set == true){
			$this->exists = true;
		} else if($set == false){
			$this->exists = false;
		} else {
			return $this->exists;
		}
	}

	private function generateIdentifier() {
		return bin2hex(random_bytes(16));
	}

	public function getAccountUUID() {
		return $this->account_uuid;
	}
}
?>
