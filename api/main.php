<?php
# !!! CAUTION - BAD ENGLISH !!! #

# CLASS > Astronauth
# This is the main file of the Astronauth API. It's basically the endpoint of all actions that are available through
# the api.

session_start();

# load config and required classes
require('config.php'); # config
require('valid8.php'); # input validation
require('account.php'); # account manager
require('session.php'); # session manager
require('login.php'); # login manager -> see login.php for details

$pdo; # PDO connection to database (TODO put into classes for security purpose)

class Astronauth {
	//global $pdo;
	private $account; # current account, instance of Account
	private $session; # current session, instance of Session
	private $login; # current login, instance of Login

	private $loginState = false; # false = user is not logged in / true = user is logged in

	public function __construct() {
		# connect to database
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	}

	public function init() {
		# This function should be called each time a new Astronauth instance is opened. It logs in the user if there
		# is an existing session or he used the 'keep logged in' function.
		# If the autologin was successful, this function returns true.
		# If the autologin was not successful, this function returns false. Then the user has to sign in using his
		# email / username and password.

		# try to log in by an existing session
		$this->session = Session::get();
		if($this->session instanceof Session){
			# there is an existing session, check if account is correct
			$this->account = Account::pull($this->sesson->getAccountUUID());
			if($this->account instanceof Account){
				# account is correct
				$this->loggedIn(true); # set the login state ($loginState) to true
				return true;
			}
		}

		# try to log in by existing login (keep logged in function)
		$this->login = Login::get();
		if($this->login instanceof Login){
			# there is an existing login, check if login data is correct
			if($this->login->verify()){
				# login is valid, check if account is correct
				$this->account = Account::pull($this->login->getAccountUUID());
				if($this->account instanceof Account){
					# account is correct
					$this->session = new Session($this->account); # open a new session
					$this->session->set(); # save this session

					$this->login->set(); # generate a new login token and save it to cookie
					$this->login->push(); # save new login data to database

					$this->loggedIn(true); # set the login state to true
					return true;
				}
			}
		}

		# there is no existing login -> user has to log in with password
		return false;
	}

	public function signIn($user, $password, $keepLoggedIn = false) {
		# This function is the manual signin procedure (using username / email and password)
		# The user can decide if he wants to use his username or his email

		# check if user is already signed in
		if($this->loggedIn()){
			return false;
		}

		# check if $user is a username or an email
		if(Valid8::email($user)){
			# it is an email, try to get the data of the account with this email
			$this->account = Account::pull($user, 'email');
		} else {
			# it is a username, try to get the data of the account with this username
			$this->account = Account::pull($user, 'username');
		}

		# check if the returned data is formally valid
		if($this->account instanceof Account){
			# check if the password is correct
			if($this->account->verify($password)){
				# it is, open new session with the user's data and save it
				$this->session = new Session($this->account);
				$this->session->set();

				if($keepLoggedIn == true){
					# user wants to be automatically signed in next time
					$this->login = new Login($this->account); # open new login
					$this->login->set(); # save the login data in cookie
					$this->login->push(); # save the login data in database
				}

				return true;
			}
		}

		return false;
	}

	public function signUp($username, $email, $password, $pwcheck) {
		# This function is the signup process to create a new account.
		# $username is the username set by the user. $email is the user's email. $password is the user's password.
		# $pwcheck is the repeated password, it must be the same as $password.

		if(!isset($username) || !isset($email) || !isset($password) || !isset($pwcheck)){
			# not all required data is provided
			return false;
		}

		# formally validate inputs
		if(!Valid8::username($username) || !Valid8::email($email) || !Valid8::password($password, $pwcheck, false)){
			# not all inputs are formally valid
			return false;
		}

		# check if username is already in use
		$usernameCheckAccount = Account::pull($username, 'username');
		if($usernameCheckAccount != false){
			return false;
		}

		# check if email is already in use
		$emailCheckAccount = Account::pull($email, 'email');
		if($emailCheckAccount != false){
			return false;
		}

		# everything seems to be valid, create account
		$this->account = Account::new($username, $email, $password);
		$this->account->push(); # push account to database

		return true;
	}

	public function signOff() {
		# This function signs the user off.

		# check if user is logged in
		if($this->loggedIn()){
			$this->session->unset();
			unset($this->session);
			$this->login->unset();
			unset($this->login);
			unset($this->account);
			$this->loggedIn(false);
			return true;
		}

		return false;
	}

	public function loggedIn($set) {
		# This function sets and returns the login state of the user
		if($set == false){ # set the login state to false
			$this->loginState = false;
		} else if($set == true){ # set the login state to true
			$this->loginState = true;
		} else { # do not set anything, just return the login state
			return $this->loginState;
		}
	}

	public function getAccountUUID() { # return the uuid of the current logged in account
		if($this->loggedIn()){
			return $account->getUUID();
		} else return false;
	}

	public function getAccountUsername() { # return the username of the current logged in account
		if($this->loggedIn()){
			return $account->getUsername();
		} else return false;
	}

	public function getAccountEmail() { # return the email of the current logged in account
		if($this->loggedIn()){
			return $account->getEmail();
		} else return false;
	}
}
?>
