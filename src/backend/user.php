<?php
namespace Astronauth\Backend;
use \Astronauth\Backend\Classes\Account;
use \Astronauth\Backend\Classes\Device;
use \Astronauth\Backend\Classes\Session;
use \Astronauth\Config\Config;
use PDO;

class User { # = Main
	public $session;
	public $account;
	public $device;

	public $pdo;

	private $is_authenticated = false;


	function __construct() {
		$this->session = new Session($this);
		$this->pdo = new PDO(
			'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME,
			Config::DB_USER,
			Config::DB_PASSWORD
		);

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
			$this->device = new Device($this->pdo);
			$this->device->read();
		} catch(\Exception $e){
			return false;
		}

		if($this->device->verify()){
			$this->device->refresh();
			$this->session->write();
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
		$this->account = new Account($this->pdo);
		$this->account->generate();

		$this->account->insert($data);
	}

	public function login($identifier, $password, $remember = false) {
		$this->account = new Account($this->pdo);
		$this->account->pull($identifier);

		if(!$this->account->verify_password($password)){
			unset($this->account);
			throw new \Exception('invalid pw');
		}

		if($remember){
			$this->device = new Device($this->pdo);
			$this->device->generate();
			$this->device->push();
			$this->device->write();
		}

		$this->is_authenticated = true;
		$this->session->write();
	}

	public function logout() {
		$this->device->archive();
		$this->device->erase();
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
