<?php
namespace Astronauth;
use \Astronauth\Model\Session;
use \Astronauth\Model\DatabaseObjects\Account;
use \Astronauth\Exceptions\AuthenticationStateException;
use \Astronauth\Exceptions\EmptyResultException;
use \InvalidArgumentException;

class Main {
	private $session;
	private $manual_mode;


	function __construct(bool $manual_mode = false) {
		$this->session = new Session();
		$this->manual_mode = $manual_mode;
	}

	public function authenticate($device_id = null, $device_token = null) {
		if($this->manual_mode){
			if(empty($device_id) || empty($device_token)){
				throw new InvalidArgumentException('Empty device_id and/or device_token; Both Required on Manual Mode.');
			}

			$this->session->read($device_id, $device_token, true);
		} else {
			$this->session->read();
		}
	}

	public function signin($identifier, $password, $remember = true) {
		if($this->is_authenticated()){
			throw new AuthenticationStateException('noauth');
		}

		try {
			$account = new Account();
			$account->pull_by_name_or_email($identifier);
		} catch(EmptyResultException $e){
			return false;
		}

		if(!$account->verify_password($password)){
			return false;
		}

		$this->session->account = $account;

		if($this->manual_mode){
			return $this->session->write('manual');
		}

		if($remember){
			$this->session->write('cookie');
		} else {
			$this->session->write('session');
		}

		return true;
	}

	public function signout() {
		if(!$this->is_authenticated()){
			throw new AuthenticationStateException('auth');
		}

		$this->session->erase();
		$this->session = new Session();
	}

	public function create_account($name, $email, $password) {
		if($this->is_authenticated()){
			throw new AuthenticationStateException('noauth');
		}

		$account = new Account();
		// TODO
	}

	public function delete_account($password) {
		if(!$this->is_authenticated()){
			throw new AuthenticationStateException('auth');
		}

		if($this->session->account->verify_password($password)){
			$this->session->account->delete();
		}
	}

	public function change_password($old, $new) {
		if(!$this->is_authenticated()){
			throw new AuthenticationStateException('auth');
		}

		if($this->session->account->verify_password($old)){
			$this->session->account->set_password($new);
		}
	}

	public function send_password_reset_email($identifier) {

	}

	public function reset_password($key, $new) {

	}

	public function get_devices() {

	}

	public function disable_device($id) {

	}

	public function is_authenticated() {
		return $this->session->authenticated;
	}

	public function get_account_name() {
		return $this->session->account->name;
	}

	public function get_account_email() {
		return $this->session->account->email;
	}
}
?>
