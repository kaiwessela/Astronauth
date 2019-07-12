<?php
session_start();
require('backend/main.php');
$astronauth = new Astronauth();

if($_GET['action'] == 'signin' || $_POST['action'] == 'signin'){
	if($astronauth->signin($_POST['user'], $_POST['password'], $_POST['keepLoggedIn'])){
		echo 'Erfolgreich angemeldet';
	} else {
		echo 'Fehler';
	}
} else if($_GET['action'] == 'signup' || $_POST['action'] == 'signup'){
	if($astronauth->signup($_POST['username'], $_POST['email'], $_POST['password'], $_POST['pwcheck'])){
		echo 'Erfolgreich registriert';
	} else {
		echo 'Fehler';
	}
} else if($_GET['action'] == 'signoff' || $_POST['action'] == 'signoff'){
	$astronauth->signoff();
} else if($_GET['action'] == 'signout' || $_POST['action'] == 'signout'){
	$astronauth->signout();
} else {

}
?>
