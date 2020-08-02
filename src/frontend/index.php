<?php
namespace Astronauth;
/*
ENDPOINTS:
/			- main page if signed in, if not signed in redirect to /signin
/signin
/signup
/signout
/account
(/admin)
*/
session_start();
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
require_once ROOT . 'share/endpoint_common.php';

$astronauth = new User();
$signedin = $astronauth->authenticate();

$endpoint = $_GET['endpoint'] ?? '';

if(!$endpoint){
	$endpoint = 'landing';
	if(!$signedin){
		header('Location: ' . SERVER_URL . '/signin');
		exit;
	}
} else if($endpoint == 'signin'){
	if($signedin){
		header('Location: ' . SERVER_URL . '/');
		exit;
	}
} else if($endpoint == 'signup'){
	if($signedin){
		header('Location: ' . SERVER_URL . '/');
		exit;
	}
} else if($endpoint == 'signout'){
	if(!$signedin){
		header('Location: ' . SERVER_URL . '/signin');
		exit;
	}
} else if($endpoint == 'account'){
	if(!$signedin){
		header('Location: ' . SERVER_URL . '/signin');
		exit;
	}
} else {
	header('Location: ' . SERVER_URL . '/');
	exit;
}

include "endpoints/$endpoint.php";

?>
