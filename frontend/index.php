<?php
session_start();
require '../backend/main.php';

$astronauth = new Astronauth();
$astronauth->initialize();

if($astronauth->isAuthenticated()){
	header('Location: ok.php');
	echo 'Sie werden weitergeleitet...';
	exit;
}

if(isset($_GET['action']) && $_GET['action'] == 'process'){
	if(!isset($_POST['astro-account-identifier'])){

	}

	if(!isset($_POST['astro-account-password'])){

	}

	
}
?>

<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8">
		<title>Anmelden – Astronauth</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<form action="?action=process" method="post">
			<h1>Anmelden</h1>
			<span class="newhere">Neu hier? <a href="signup.php">Registrieren</a></span>
			<label>
				<span class="label-field">Benutzername oder E-Mail-Adresse</span>
				<input type="text" name="astro-account-identifier">
				<div class="indicator"></div>
			</label>
			<label>
				<span class="label-field">Passwort</span>
				<input type="password" name="astro-account-password">
				<div class="indicator"></div>
			</label>
			<a class="under-input" href="forgotpassword.php">Passwort vergessen?</a>
			<label class="checkbodge turn-around">
				<span class="label-field">Angemeldet bleiben</span>
				<input type="checkbox" name="astro-remember-me">
				<span class="bodgecheckbox">
					<span class="bodgetick">
						<span class="bodgetick-down"></span>
						<span class="bodgetick-up"></span>
					</span>
				</span>
			</label>
			<input type="submit" value="Anmelden">
		</form>
		<script src="script.js"></script>
	</body>
</html>
