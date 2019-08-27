<?php
class Tools {
	public static function uuid() {
		$hex = bin2hex(random_bytes(16));
		$chu = explode(' ', chunk_split($hex, 4, ' '));
		return $chu[0].$chu[1].'-'.$chu[2].'-'.$chu[3].'-'.$chu[4].'-'.$chu[5].$chu[6].$chu[7];
	}

	public static function hash($password) {
		return password_hash($password, PASSWORD_DEFAULT);
	}
}
?>
