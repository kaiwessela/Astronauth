<?php
namespace Astronauth\Exceptions;
use Exception;

class AuthenticationStateException extends Exception {
	function __construct($required = null) {
		$message = 'Wrong Authentication State';

		if($required == 'auth'){
			$message .= ': must be authenticated.';
		} else if($required == 'noauth'){
			$message .= ': must not be authenticated.';
		}

		parent::__construct($message);
	}
}
?>
