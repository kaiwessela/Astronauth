<?php
session_start();

require 'backend/main.php';

$astronauth = new Astronauth();
$astronauth->initialize();

switch ($_GET['action']) {
	case 'login':
		$astronauth->tryToSignIn();
		break;

	case 'register':
		$astronauth->signUp();
		break;

	case 'logout':
		$astronauth->signOut();
		break;

	default:
		return false;
		break;
}

echo '<br><br><br>';
echo implode('<br>', $e->display());
?>
