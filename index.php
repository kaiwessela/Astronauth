<?php
session_start();
require('api/main.php');
$astronauth = new Astronauth();
$astronauth->start();

if(isset($_GET['tab']) && $_GET['tab'] == 'signup'){
	$tab = 'signup';
} else if(isset($_GET['tab']) && $_GET['tab'] == 'signoff'){
	$tab = 'signoff';
} else {
	$tab = 'signin';
}

if($astronauth->userIsLoggedIn()){
	$tab = 'signoff'; # override tab if user is logged in
} else {
	if($tab == 'signoff'){
		$tab = 'signin'; # override tab if user is not logged in but has signup tab selected
	}
}

if($tab == 'signin'){
	$SigninTabState = 'active';
	$SignupTabState = 'enabled';
	$SignoffTabState = 'disabled';

	$SigninState = 'active';
	$SignupState = '';
	$SignoffState = '';
} else if($tab == 'signup'){
	$SigninTabState = 'enabled';
	$SignupTabState = 'active';
	$SignoffTabState = 'disabled';

	$SigninState = '';
	$SignupState = 'active';
	$SignoffState = '';
} else if($tab == 'signoff'){
	$SigninTabState = 'disabled';
	$SignupTabState = 'disabled';
	$SignoffTabState = 'active';

	$SigninState = '';
	$SignupState = '';
	$SignoffState = 'active';
}

?>
<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body class="astro astro-body">
		<div class="astro astro-bar">
			<div class="astro astro-tab <?php echo $SigninTabState; //active ?>" id="astro-tab-signin">
				<a href="?tab=signin" id="astro-tab-link-signin" data-type="signin">
					<h1>Anmelden</h1>
				</a>
			</div>
			<div class="astro astro-tab <?php echo $SignupTabState; //enabled ?>" id="astro-tab-signup">
				<a href="?tab=signup" id="astro-tab-link-signup" data-type="signup">
					<h1>Registrieren</h1>
				</a>
			</div>
			<div class="astro astro-tab <?php echo $SignoffTabState; //disabled ?>" id="astro-tab-signoff">
				<a href="?tab=signoff" id="astro-tab-link-signoff" data-type="signoff">
					<h1>Abmelden</h1>
				</a>
			</div>
		</div>
		<div id="astro-signin" class="astro astro-box <?php echo $SigninState; ?>"  data-type="signin">
			<form id="astro-signin-form" action="api/endpoint.php?action=signin" method="POST" data-type="signin">
				<label for="astro-signin-user">Benutzername oder E-Mail-Adresse</label><br>
				<input type="text" id="astro-signin-user" name="user"><br>
				<label for="astro-signin-password">Passwort</label><br>
				<input type="password" id="astro-signin-password" name="password"><br>
				<input type="checkbox" id="astro-signin-keepLoggedIn" name="keepLoggedIn">
				<label for="astro-signin-keepLoggedIn">Angemeldet bleiben</label><br>
				<input type="submit" value="Anmelden">
			</form>
		</div>
		<div id="astro-signup" class="astro astro-box <?php echo $SignupState; ?>" data-type="signup">
			<form id="astro-signup-form" action="api/endpoint.php?action=signup" method="POST" data-type="signup">
				<label for="astro-signup-username">Benutzername</label><br>
				<input type="text" id="astro-signup-username" name="username"><br>
				<label for="astro-signup-email">E-Mail-Adresse</label><br>
				<input type="email" id="astro-signup-email" name="email"><br>
				<label for="astro-signup-password">Passwort</label><br>
				<input type="password" id="astro-signup-password" name="password"><br>
				<label for="astro-signup-pwcheck">Passwort wiederholen</label><br>
				<input type="password" id="astro-signup-pwcheck" name="pwcheck"><br>
				<input type="submit" value="Registrieren">
			</form>
		</div>
		<div id="astro-signoff" class="astro astro-box <?php echo $SignoffState; ?>" data-type="signoff">
			<form id="astro-signoff-form" action="api/endpoint.php?action=signoff" method="POST" data-type="signoff">
				<input type="submit" value="Abmelden">
			</form>
		</div>
		<script src="script.js"></script>
	</body>
</html>
