<?php
require('api/main.php');

$astronauth = new Astronauth();
$astronauth->init();

// TODO error messages from main.php

if($_GET['action'] == 'signin' || $_POST['action'] == 'signin'){
	if(isset($_POST['user']) && isset($_POST['password'])){
		if($astronauth->signIn($_POST['user'], $_POST['password']) == true){ // TODO keeploggedin
			$echo['status'] = 'Signin successful.';
			$echo['message'] = 'Signed in as "'.$astronauth->getAccountUsername().'"';
			$echo['result']['uuid'] = $astronauth->getAccountUUID();
			$echo['result']['username'] = $astronauth->getAccountUsername();
			$echo['result']['email'] = $astronauth->getAccountEmail();
		} else {
			$echo['status'] = 'Signin failed.';
			$echo['message'] = 'Reason: wrong username, email and/or password';
		}
	} else {
		$echo['status'] = 'Signin failed.';
		$echo['message'] = 'Reason: invalid post data';
	}
} else if($_GET['action'] == 'signup' || $_POST['action'] == 'signup'){
	if($astronauth->loggedIn()){
		$echo['status'] = 'Signup failed.';
		$echo['message'] = 'Reason: already signed in';
	} else {
		if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['pwcheck'])){
			if($astronauth->signUp($_POST['username'], $_POST['email'], $_POST['password'], $_POST['pwcheck']) == true){
				$echo['status'] = 'Signup successful.';
				$echo['message'] = 'Account with username "'.$astronauth->getAccountUsername().'" created';
			} else {
				$echo['status'] = 'Signup failed.';
				$echo['message'] = 'Reason: wrong data';
			}
		} else {
			$echo['status'] = 'Signup failed.';
			$echo['message'] = 'Reason: invalid post data';
		}
	}
} else if($_GET['action'] == 'signoff' || $_POST['action'] == 'signoff'){
	if($astronauth->signOff() == true){
		$echo['status'] = 'Signoff successful.';
		$echo['message'] = 'signed off';
	} else {
		$echo['status'] = 'Signoff failed.';
		$echo['message'] = 'Reason: idk';
	}
} else if($_GET['action'] == 'status' || $_GET['action'] == 'status'){
	if($astronauth->loggedIn()){
		$echo['status'] = 'Signed in.';
		$echo['result']['uuid'] = $astronauth->getAccountUUID();
		$echo['result']['username'] = $astronauth->getAccountUsername();
		$echo['result']['email'] = $astronauth->getAccountEmail();
	} else {
		$echo['status'] = 'Signed off.';
	}
} else {
	$echo['status'] = 'Error.';
	$echo['message'] = 'invalid action or no action specified';
}

if($_GET['format'] == 'plain' || $_POST['format'] == 'plain'){
	echo $echo['status'];
	echo $echo['message'];
	echo $echo['result']['uuid'];
	echo $echo['result']['username'];
	echo $echo['result']['email'];
} else {
	echo json_encode($echo);
}
?>
