<?php
namespace Astronauth\Frontend\Web;
use \Astronauth\Backend\User;
use \Astronauth\Config\Config;

class Endpoint {
	public $user;


	function __construct() {
		$this->user = new User();
	}

	public function handle() {
		$this->user->authenticate();

		$endpoint = $_GET['endpoint'] ?? '';

		if(!$endpoint){
			$endpoint = 'landing';
			$this->sufficiency_authenticated('signin');

			$username = $this->user->account->name;

		} else if($endpoint == 'signin'){
			$this->sufficiency_unauthenticated();

			if($_POST){
				try {
					$this->user->login($_POST['identifier'], $_POST['password'], (bool) $_POST['remember'] ?? false);
					$this->redirect('');
					$show_error = false;
					$show_form = false;
					$show_success = true;
				} catch(\Exception $e){
					$show_error = true;
					$show_form = true;
					$show_success = false;
				}
			} else {
				$show_error = false;
				$show_form = true;
				$show_success = false;
			}

		} else if($endpoint == 'signup'){
			$this->sufficiency_unauthenticated();

			if(Config::SIGNUP_CLOSED){ // TEMP
				$this->redirect('signin');
			}

			if($_POST){
				try {
					$this->user->register($_POST);
					$show_error = false;
					$show_form = false;
					$show_success = true;
				} catch(InvalidInputException $e){
					$show_error = true;
					$show_form = true;
					$show_success = false;
					$error_message = $e->getMessage();
				} catch(ObjectAlreadyExistsException $e){
					$show_error = true;
					$show_form = true;
					$show_success = false;
					$error_message = 'Benutzername oder E-Mail-Adresse ist bereits registriert.';
				} catch(DatabaseException $e){
					$show_error = true;
					$show_form = false;
					$show_success = false;
					$error_message = 'Interner Datenbankfehler.';
				}
			} else {
				$show_error = false;
				$show_form = true;
				$show_success = false;
			}

		} else if($endpoint == 'signout'){
			$this->sufficiency_authenticated();

			if($_POST){
				$this->user->logout();
				$this->redirect('signin');
			}

		} else if($endpoint == 'account'){
			$this->sufficiency_authenticated();

		} else {
			$this->redirect('');
			exit;
		}

		$server = (object) [
			'url' => Config::SERVER_URL,
			'lang' => Config::SERVER_LANG,
			'redirect_url' => Config::REDIRECT_URL
		];

		include "frontend/endpoints/$endpoint.php";
	}

	public function redirect($location = '') {
		header('Location: ' . Config::SERVER_URL . '/' . $location);
	}

	public function sufficiency_authenticated($redirect = '') {
		if(!$this->user->is_authenticated()){
			$this->redirect($redirect);
			exit;
		}
	}

	public function sufficiency_unauthenticated($redirect = '') {
		if($this->user->is_authenticated()){
			$this->redirect($redirect);
			exit;
		}
	}
}
?>
