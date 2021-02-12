<?php
namespace Astronauth\Backend;
use \Astronauth\Backend\Models\Account;
use \Astronauth\Backend\Models\Device;
use \Astronauth\Backend\Session;
use \Astronauth\Config\Config;
use PDO;

class User { # = Main
	public $session;
	public $account;
	public $device;

	private $is_authenticated = false; // BUG-GY


	function __construct() {
		$this->session = new Session($this);

		setlocale(\LC_ALL, Config::SERVER_LANG . '.utf-8');

		if(Config::DEBUG_MODE){
			ini_set('display_errors', '1');
			error_reporting(\E_ALL);
		} else {
			ini_set('display_errors', '0');
			error_reporting(0);
		}
	}

	public function authenticate() {
		if($this->session->read()){
			$this->is_authenticated = true;
			return true;
		} else {
			$this->session->clear();
		}

		try {
			$this->device = new Device();
			$this->device->read();
		} catch(\Exception $e){
			return false;
		}

		if($this->device->verify()){
			$this->device->refresh();
			$this->session->write();

			$this->account = new Account();
			$this->account->pull_by_id($this->device->account_id);

			$this->is_authenticated = true;
			return true;
		} else {
			if(!$this->device->is_empty()){
				$this->device->archive();
			}

			$this->session->clear();
			return false;
		}
	}

	public function register($data) {
		$this->account = new Account();
		$this->account->generate();

		$this->account->insert($data);
	}

	public function login($identifier, $password, $remember = false) {
		$this->account = new Account();
		$this->account->pull($identifier);

		if(!$this->account->verify_password($password)){
			unset($this->account);
			throw new \Exception('invalid pw');
		}

		if($remember){
			$this->device = new Device();
			$this->device->generate($this->account->id);
			$this->device->push();
			$this->device->write();
		}

		$this->is_authenticated = true;
		$this->session->write();
	}

	public function logout() {
		if($this->device->is_active()){
			$this->device->archive();
			$this->device->erase();
		}

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
