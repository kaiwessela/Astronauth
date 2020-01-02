<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8">
		<title>Astronauth</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<form action="?" method="post">
			<h1>Registrieren</h1>
			<span class="newhere">Schon registriert? <a href="signin.html">Anmelden</a></span>
			<label>
				<span class="label-field">Benutzername</span>
				<span class="label-hint">Wählen Sie einen Benutzernamen für ihr Konto.</span>
				<span class="label-hint">Der Benutzername muss aus 8 bis 30 Zeichen bestehen und darf nur reguläre
					Buchstaben, Zahlen, Punkte (.) oder Unterstriche (_) enthalten.
				<input type="text" name="astro-account-username" required>
				<div class="indicator"></div>
			</label>
			<label>
				<span class="label-field">E-Mail-Adresse</span>
				<span class="label-hint">Geben Sie eine E-Mail-Adresse an, unter der wir Sie kontaktieren können.</span>
				<span class="label-hint">Sie erhalten zum Beispiel eine E-Mail, wenn Sie Ihr Passwort vergessen haben.
					Wir werden Ihnen ohne Ihre Zustimmung keine Werbung zusenden.</span>
				<input type="email" name="astro-account-email" required>
				<div class="indicator"></div>
			</label>
			<label>
				<span class="label-field">Passwort</span>
				<span class="label-hint">Geben Sie ein sicheres Passwort an, mit dem Sie sich anmelden können.</span>
				<span class="label-hint">Ein sicheres Passwort hat mindestens 8 Zeichen und ist möglichst zufällig gewählt.</span>
				<input type="password" name="astro-account-password" required>
				<div class="indicator"></div>
			</label>
			<label class="checkbodge turn-around">
				<span class="label-field">
					Ich habe die <a href="agb" target="_blank">Allgemeinen Geschäftsbedingungen</a> gelesen und akzeptiere sie.
				</span>
				<input type="checkbox" name="astro-remember-me" required>
				<span class="bodgecheckbox">
					<span class="bodgetick">
						<span class="bodgetick-down"></span>
						<span class="bodgetick-up"></span>
					</span>
				</span>
			</label>
			<input type="submit" value="Registrierung abschließen">
		</form>
		<script src="script.js"></script>
	</body>
</html>
