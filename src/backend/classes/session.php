<?php
namespace Astronauth\Backend\Classes;

class Session {
	private $user;

	const ACCOUNT_KEYWORD = 'astronauth_account';
	const DEVICE_KEYWORD = 'astronauth_device';


	function __construct(&$user) {
		$this->user = &$user;
	}

	public function read() {
		if(!isset($_SESSION[self::ACCOUNT_KEYWORD])){
			return false;
		}

		$account = unserialize($_SESSION[self::ACCOUNT_KEYWORD]);

		if(!$account instanceof Account){
			return false;
		}

		$account->goodmorning($this->user->pdo);

		$this->user->account = $account;

		if(isset($_SESSION[self::DEVICE_KEYWORD])){
			$device = unserialize($_SESSION[self::DEVICE_KEYWORD]);

			if($device instanceof Device){
				$device->goodmorning($this->user->pdo);
				$this->user->device = $device;
			}
		}

		return true;
	}

	public function write() {
		if($this->user->account){
			$this->user->account->goodnight();
			$_SESSION[self::ACCOUNT_KEYWORD] = serialize($this->user->account);
		}

		if($this->user->device){
			$this->user->device->goodnight();
			$_SESSION[self::DEVICE_KEYWORD] = serialize($this->user->device);
		}
	}

	public function clear() {
		unset($_SESSION[self::ACCOUNT_KEYWORD]);
		unset($_SESSION[self::DEVICE_KEYWORD]);
	}
}
?>
