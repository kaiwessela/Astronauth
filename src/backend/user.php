<?php
namespace Astronauth;

class User {
	public $session;
	public $account;
	public $device;
	private $is_authenticated = false;


	function __construct() {
		$this->session = new Session($this);
	}

	public function authenticate() {
		if($this->session->read()){
			$this->is_authenticated = true;
			return true;
		} else {
			$this->session->clear();
		}

		try {
			$this->device = Device::read();
		} catch(\Exception $e){
			return false;
		}

		if($this->device->verify_token()){
			$this->device->refresh();
			$this->session->write();
			$this->is_authenticated = true;
			return true;
		} else {
			$this->device->archive();
			$this->session->clear();
			return false;
		}
	}

	public function register($data) {
		$this->account = Account::new();

		try {
			$this->account->insert($data);
		} catch(\Exception $e){
			return false;
		}

		return true;
	}

	public function login($identifier, $password, $remember = false) {
		#try {
			$this->account = Account::pull($identifier);
		#} catch(Exception $e){

		#}

		if(!$this->account->verify_password($password)){
			unset($this->account);
			throw new \Exception('invalid pw');
		}

		if($remember){
			$this->device = Device::generate();
			$this->device->set_cookies();
			// TODO push
		}

		$this->session->write();

		return true;

	}

	public function logout() {
		$this->device->archive();
		$this->device->unset_cookies();
		$this->session->clear();
		unset($this->session);
		unset($this->account);
		unset($this->device);
	}

	public function is_authenticated() {
		return $this->is_authenticated;
	}
}
?>
