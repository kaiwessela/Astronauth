<?php
$login_failed = null;
if($_POST){
	try {
		$astronauth->login($_POST['identifier'], $_POST['password'], (bool) $_POST['remember']);
	} catch(Exception $e){
		$login_failed = true;
	}

	if(!$login_failed){
		$login_failed = false;
		header('Location: ' . ASTRONAUTH_URL . '/');
	}
}
?>
<!DOCTYPE html>
<html lang="de">
	<head>
		<?php include 'templates/head.tmp.php'; ?>
		<title>Astronauth</title>
	</head>
	<body>
		<main>
			<h1>Anmelden</h1>
			<a href="signup">Neu hier? Registrieren</a>

<?php if($login_failed){ ?>
			<section class="message error">
				<p class="summary">Anmeldung fehlgeschlagen</p>
				<p>Benutzer existiert nicht oder Passwort falsch.</p>
			</section>
<?php } ?>

<?php if($login_failed || !$_POST){ ?>
			<form action="#" method="post">
				<label for="identifier">Benutzername oder E-Mail-Adresse</label>
				<input type="text" id="identifier" name="identifier" required>
				<label for="password">Passwort</label>
				<input type="password" id="password" name="password" required>
				<a href="forgot" class="under-input">Passwort vergessen?</a>
				<label class="checkbodge turn-around">
					<span class="label-field">Angemeldet bleiben</span>
					<input type="checkbox" name="remember" value="true">
					<span class="bodgecheckbox">
						<span class="bodgetick">
							<span class="bodgetick-down"></span>
							<span class="bodgetick-up"></span>
						</span>
					</span>
				</label>
				<input type="checkbox" id="remember" name="remember">
				<input type="submit" value="Anmelden">
			</form>
<?php } ?>

<?php if($_POST && !$login_failed){ ?>
			<section class="message success">
				<p class="summary">Anmeldung erfolgreich</p>
				<p>Sie werden jeden Moment weitergeleitet.</p>
			</section>
<?php } ?>

		</main>
	</body>
</html>
