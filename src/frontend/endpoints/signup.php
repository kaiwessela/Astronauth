<?php
if($_POST){
	$astronauth->register($_POST);
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
			<h1>Account erstellen</h1>
			<a href="signin">Mit bestehendem Account anmelden</a>
			<form action="#" method="post">
				<label for="name">
					<span class="label-field">Benutzername</span>
					<span class="label-hint">
						4 bis 32 Stellen, erlaubt sind Buchstaben, Zahlen, Punkte (.) und
						Binde- und Unterstriche (- und _).
					</span>
				</label>
				<input type="text" id="name" name="name" required>
				<label for="email">
					<span class="label-field">E-Mail-Adresse</span>
					<span class="label-hint">
						Die E-Mail-Adresse benötigen wir für Dings und Bums.
					</span>
				</label>
				<input type="email" id="email" name="email" required>
				<label for="password">
					<span class="label-field">Passwort</span>
					<span class="label-hint">
						8 bis 128 Zeichen. <a href="#">Tipps für ein sicheres Passwort</a>
					</span>
				</label>
				<input type="password" id="password" name="password" required>
				<input type="submit" value="Account erstellen">
			</form>
		</main>
	</body>
</html>
