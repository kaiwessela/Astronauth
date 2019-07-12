<?php
class Log {
	public static $events;

	public static function throw($code, $text) {

	}
}

class Info extends Log {
	public $prefix = 'INF-';

	public static function throw($code, $text) {

	}
}

class Warning extends Log {
	public $prefix = 'WRN-';

	public static function throw($code, $text) {

	}
}

/*class Error extends Log {
	public $prefix = 'ERR-';

	public static function throw($code, $text) {

	}
}*/

/* +++ CODES +++

INF-
	USERNAME-
		VALID
	EMAIL-
		VALID
	HIBP-
		BEGIN-CHECK
		SUCCESS

WRN-
	HIBP-
		UNAVAILABLE
		PASSWORD-COMPROMISED

ERR-
	USERNAME-
		INVALID-
			LENGTH
			CHARS
	EMAIL-
		INVALID
		EXISTS
	PASSWORD-
		INVALID-
			LENGTH
	PASSWORDS-
		NOT-MATCHING
	HIBP-
		FAILURE


   --- codes --- */
?>
