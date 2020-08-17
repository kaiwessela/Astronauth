<?php
namespace Astronauth\Backend;
use \Astronauth\Backend\Models\Account;
use \Astronauth\Backend\Models\Device;

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

		$this->user->account = $account;

		if(isset($_SESSION[self::DEVICE_KEYWORD])){
			$device = unserialize($_SESSION[self::DEVICE_KEYWORD]);

			if($device instanceof Device){
				$this->user->device = $device;
			}
		}

		return true;
	}

	public function write() {
		if($this->user->account){
			$_SESSION[self::ACCOUNT_KEYWORD] = serialize($this->user->account);
		}

		if($this->user->device){
			$_SESSION[self::DEVICE_KEYWORD] = serialize($this->user->device);
		}
	}

	public function clear() {
		unset($_SESSION[self::ACCOUNT_KEYWORD]);
		unset($_SESSION[self::DEVICE_KEYWORD]);
	}
}
?>
