<?php
$possibleExceptions = [
	0020 => new AstroEx(2, 'exception.invalid', ''),
	1410 => new AstroEx(1, 'main.signUp.begin', ''),
	1431 => new AstroEx(3, 'main.signUp.alreadyAuthenticated', ''),
	1432 => new AstroEx(3, 'main.signUp.setUsernameFailed', ''),
	1433 => new AstroEx(3, 'main.signUp.setEmailFailed', ''),
	1434 => new AstroEx(3, 'main.signUp.passwordHashFailed', ''),
	1435 => new AstroEx(3, 'main.signUp.passwordInsertFailed', ''),
	1436 => new AstroEx(3, 'main.signUp.accountInsertFailed', ''),
	1411 => new AstroEx(1, 'main.signUp.complete', ''),
];

class ExceptionHandler {
	public $exceptions = [];

	public function throw($exception) {
		global $possibleExceptions;

		if($exception instanceof AstroEx){
			$this->exceptions[] = $exception;
		} else if(is_int($exception)){
			$this->exceptions[] = $possibleExceptions[$exception];
		} else {
			$this->exceptions[] = new AstroEx(AstroEx::LEVEL_WARN, 000, 'exception.invalid', 'ungültiger Fehler');
		}
	}

	public function display() {
		$output = [];
		foreach($this->exceptions as $exception){
			$output[] = $exception->display();
		}

		return $output;
	}
}

class AstroEx {
	public $level;
	public $name;
	public $message;
	public $timestamp;

	const LEVEL_INFO = 0;
	const LEVEL_DEBUG = 1;
	const LEVEL_WARN = 2;
	const LEVEL_ERROR = 3;

	function __construct($level, $name, $message) {
		$allowedLevels = [self::LEVEL_INFO, self::LEVEL_DEBUG, self::LEVEL_WARN, self::LEVEL_ERROR];
		if(in_array($level, $allowedLevels)){
			$this->level = $level;
		} else {
			$this->level = self::LEVEL_INFO;
		}

		if(is_string($name)){
			$this->name = $name;
		}

		$this->message = $message;

		$this->timestamp = time();
	}

	public function display() {
		$time = date('H:i:s', $this->timestamp);

		switch($this->level){
			case self::LEVEL_INFO:
				$lvl = 'INFO';
				break;
			case self::LEVEL_DEBUG:
				$lvl = 'DEBUG';
				break;
			case self::LEVEL_WARN:
				$lvl = 'WARNING';
				break;
			case self::LEVEL_ERROR:
				$lvl = 'ERROR';
				break;
		}

		$output = $timestamp . ' | [' . $lvl . '] ' . $this->name . ' > ' . $this->message . ';  ';

		return $output;

		/*
			09:16:45 | [ERROR]: Program fail > program execution failed fatally;
		*/

	}

	public function arrayify() {
		$array = [
			'level' => $this->level,
			'name' => $this->name,
			'message' => $this->message,
			'timestamp' => $this->timestamp
		];

		return $array;
	}

	public function jsonify() {
		$values = json_encode($this->arrayify());
	}
}
?>
