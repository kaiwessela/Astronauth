<!DOCTYPE html>
<html lang="de">
	<head>
		<?php include 'frontend/templates/head.tmp.php'; ?>
		<title>Astronauth</title>
	</head>
	<body>
		<main>
			<h1>Anmelden</h1>

<?php if($show_form){ ?>
			<a href="signup">Neu hier? Registrieren</a>
<?php } ?>

<?php if($show_error){ ?>
			<section class="message error">
				<p class="summary">Anmeldung fehlgeschlagen</p>
				<p>Benutzer existiert nicht oder Passwort falsch.</p>
			</section>
<?php } ?>

<?php if($show_form){ ?>
			<form action="#" method="post">
				<label for="identifier">Benutzername oder E-Mail-Adresse</label>
				<input type="text" id="identifier" name="identifier" required>
				<label for="password">Passwort</label>
				<input type="password" id="password" name="password" required>
				<a href="forgot" class="under-input">Passwort vergessen?</a>
				<label class="checkbodge turn-around">
					<span class="label-field">Angemeldet bleiben</span>
					<input type="checkbox" id="remember" name="remember" value="true">
					<span class="bodgecheckbox">
						<span class="bodgetick">
							<span class="bodgetick-down"></span>
							<span class="bodgetick-up"></span>
						</span>
					</span>
				</label>
				<input type="submit" value="Anmelden">
			</form>
<?php } ?>

<?php if($show_success){ ?>
			<section class="message success">
				<p class="summary">Anmeldung erfolgreich</p>
				<p>Sie werden jeden Moment weitergeleitet.</p>
			</section>
<?php } ?>

		</main>
	</body>
</html>
