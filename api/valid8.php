<?php
# VALID8 #
class Valid8 {
	public static function username($username) {
		if(strlen($username) < 3 || strlen($username) > 20){
			return false;
		}

		if(!preg_match('/^[a-zA-Z0-9-_.]*$/', $username)){
			return false;
		}

		return true;
	}

	public static function email($email) {
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			return false;
		}

		return true;
	}

	public static function password($password, $pwcheck, $hibp) {
		if($password != $pwcheck){
			return false;
		}

		if(strlen($password) < 8 || strlen($password) > 72){
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
			}

			if(strpos($result, $suffix)){
				$success = false;
			}

			if($success != true){
				return false;
			}
		}

		return true;
	}
}
?>
