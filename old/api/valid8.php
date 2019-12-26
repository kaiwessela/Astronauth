<?php
# VALID8 #
class Valid8 { # DEPRECATED

	private static $currentMessages = array();

	private static function throwMessage($message) {
		self::$currentMessages[] = $message;
	}

	private static function clearMessages() {
		self::$currentMessages = array();
	}

	public static function getMessages() {
		return self::$currentMessages;
	}

	public static function username($username) {
		self::clearMessages(); // TODO: this everywhere

		if(strlen($username) < 3 || strlen($username) > 20){
			self::throwMessage('username.invalid.length');
			return false;
		}

		if(!preg_match('/^[a-zA-Z0-9-_.]*$/', $username)){
			self::throwMessage('username.invalid.characters');
			return false;
		}

		return true;
	}

	public static function email($email) {
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			self::throwMessage('email.invalid.characters');
			return false;
		}

		return true;
	}

	public static function password($password, $pwcheck, $hibp = false) {
		if($password != $pwcheck){
			self::throwMessage('password.doesntmatch');
			return false;
		}

		if(strlen($password) < 8 || strlen($password) > 72){
			self::throwMessage('password.invalid.length');
			return false;
		}

		if($hibp){
			$success = true;

			$sha1 = sha1($password);
			$prefix = substr($sha1, 0, 5);
			$suffix = substr($sha1, 5);

			$result = file_get_contents('https://api.pwnedpasswords.com/range/'.$prefix);

			if(!$result){
				$success = false;
				self::throwMessage('password.hibp.failed');
			}

			if(strpos($result, $suffix)){
				$success = false;
				self::throwMessage('password.hibp.compromised');
			}

			if($success != true){
				return false;
			}
		}

		return true;
	}
}
?>
