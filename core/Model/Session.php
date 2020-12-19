<?php
namespace Astronauth\Model;
use \Astronauth\Config\Config;
use \Astronauth\Model\DatabaseObjects\Account;
use \Astronauth\Model\DatabaseObjects\Device;
use \Astronauth\Exceptions\AuthenticationStateException;
use \Astronauth\Exceptions\EmptyResultException;

class Session {
	public $account;
	public $device;

	public $mode;
	public $authenticated;


	function __construct() {
		$this->authenticated = false;
		$this->account = new Account();
		$this->device = new Device();
	}

	public function read($device_id = null, $device_token = null, $manual_only = false) {
		if(!empty($device_id)){
			$this->mode = 'manual';
		} else if(!empty($_SESSION['astronauth_id']) && !$manual_only){
			$this->mode = 'session';
			$device_id = $_SESSION['astronauth_id'];
			$device_token = $_SESSION['astronauth_token'];
		} else if(!empty($_COOKIE['astronauth_id']) && !$manual_only){
			$this->mode = 'cookie';
			$device_id = $_COOKIE['astronauth_id'];
			$device_token = $_COOKIE['astronauth_token'];
		} else {
			$this->authenticated = false;
			return false;
		}

		if(empty($device_token)){
			$this->authenticated = false;
			return false;
		}

		try {
			$this->device->pull($device_id);
		} catch(EmptyResultException $e){
			$this->device = new Device();
			$this->authenticated = false;
			return false;
		}

		if(!$this->device->verify($device_token)){
			$this->authenticated = false;
			return false;
		}

		if($this->mode == 'cookie'){
			$this->write_cookies($this->device->refresh());
		}

		$this->account = &$this->device->account;
		$this->authenticated = true;
		return true;
	}

	public function write($mode = null) {
		$this->mode = $mode;

		if($this->account->is_empty()){
			throw new AuthenticationStateException('auth');
		}

		$this->device = new Device();
		$token = $this->device->create($this->account);
		$this->device->push();

		if($this->mode == 'manual'){
			return $token;
		}

		$this->write_cookies($token);
	}

	public function erase() {
		if($this->mode != 'manual'){
			setcookie('astronauth_id', 'expired', 1);
			setcookie('astronauth_token', 'expired', 1);

			$_SESSION['astronauth_id'] = '';
			$_SESSION['astronauth_token'] = '';
		}

		$this->device->deactivate();
	}

	private function write_cookies($token){
		if($this->mode == 'cookie'){
			setcookie('astronauth_id', $this->device->id, time() + Config::COOKIE_ENDURANCE * 24 * 3600);
			setcookie('astronauth_token', $token, time() + Config::COOKIE_ENDURANCE * 24 * 3600);
		}

		$_SESSION['astronauth_id'] = $this->device->id;
		$_SESSION['astronauth_token'] = $token;
	}
}
?>
