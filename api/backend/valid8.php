<?php
# VALID8 #
class Valid8 {
	public static function username($username) {
		global $astronauth;

		if(strlen($username) < 3 || strlen($username) > 20){
			#Error::throw('USERNAME-INVALID-LENGTH', 'This username is too long or too short; Its
			#	length has to be between 3 and 20 characters');
			return false;
		}

		if(!preg_match('/^[a-zA-Z0-9-_.]*$/', $username)){
			#Error::throw('USERNAME-INVALID-CHARS', 'This username contains invalid characters;
			#	It can only contain those characters: A-Z, a-z, 0-9, _, . and -');
			return false;
		}

		$s = $astronauth->pdo->prepare('SELECT id FROM accounts WHERE username = ?');
		$s->execute(array($username));
		if($s->rowCount() != 0){
			#Error::throw('USERNAME-EXISTS', 'This username is already in use');
			return false;
		}

		#Info::throw('USERNAME-VALID', 'This username is valid');
		return true;
	}

	public static function email($email) {
		global $astronauth;

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			#Error::throw('EMAIL-INVALID', 'This is not an email adress');
			return false;
		}

		$s = $astronauth->pdo->prepare('SELECT id FROM accounts WHERE email = ?');
		$s->execute(array($email));
		if($s->rowCount() != 0){
			#Error::throw('EMAIL-EXISTS', 'This email adress is already in use');
			return false;
		}

		#Info::throw('EMAIL-VALID', 'This email adress is valid');
		return true;
	}

	public static function password($password, $pwcheck, $hibp) {
		if($password != $pwcheck){
			#Error::throw('PASSWORDS-NOT-MATCHING', 'The password inputs dont match');
			return false;
		}

		if(strlen($password) < 8 || strlen($password) > 72){
			#Error::throw('PASSWORD-INVALID-LENGTH', 'This password is too short or too long; Its
			#	length has to be between 8 and 72 characters');
			return false;
		}

		if($hibp){
			#Info::throw('HIBP-BEGIN-CHECK', 'Beginning to check if the password is compromised');

			$success = true;

			$sha1 = sha1($password);
			$prefix = substr($sha1, 0, 5);
			$suffix = substr($sha1, 5);

			$result = file_get_contents('https://api.pwnedpasswords.com/range/'.$prefix);

			if(!$result){
				#Warning::throw('HIBP-UNAVAILABLE', 'The HaveIBeenPwned API is currently unavailable');

				$success = false;
			}

			if(strpos($result, $suffix)){
				#Warning::throw('HIBP-PASSWORD-COMPROMISED', 'This password is listed in the
				#	HaveIBeenPwned database of compromised passwords');

				$success = false;
			}

			if($success == true){
				#Info::throw('HIBP-SUCCESS', 'This password is not listed as compromised');
			} else {
				#Error::throw('HIBP-FAILURE', 'Unable to confirm that this password is not compromised');

				if($astronauth->config['force-hibp-success']){
					return false;
				}
			}
		}

		return true;
	}
}
?>
