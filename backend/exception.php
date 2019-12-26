<?php
class ExceptionHandler {
	public $exceptions = [];

	public function throw($exception) {
		if($exception instanceof Exception){
			$exceptions[] = $exception;
		}
	}
}

class Exception {
	public $level;
	public $code;
	public $name;
	public $message;
	public $timestamp;

	const LEVEL_INFO = 0;
	const LEVEL_DEBUG = 1;
	const LEVEL_WARN = 2;
	const LEVEL_ERROR = 3;

	function __construct($level, $code, $name, $message) {
		$allowedLevels = [self::LEVEL_INFO, self::LEVEL_DEBUG, self::LEVEL_WARN, self::LEVEL_ERROR];
		if(in_array($level, $allowedLevels)){
			$this->level = $level;
		} else {
			$this->level = self::LEVEL_INFO;
		}

		if(is_string($code) && strlen($code) <= 3){
			$this->code = $code;
		} else {
			$this->code = '000';
		}

		if(is_string($name)){
			$this->name = $name;
		}

		$this->message = $message;

		$this->timestamp = time();
	}

	public function display() {
		$output = date('H:i:s', $this->timestamp) . ' | ';

		switch($this->level){
			case self::LEVEL_INFO: $output .= 'INFO';
			case self::LEVEL_DEBUG: $output .= 'DEBUG';
			case self::LEVEL_WARN: $output .= 'WARNING';
			case self::LEVEL_ERROR: $output .= 'ERROR';
		}

		$output .= ': [' . $this->code . '] ' . $this->name . ' > ' . $this->message . ';  ';

		return $output;

		/*
			09:16:45 | ERROR: [245] Program fail > program execution failed fatally;
		*/

	}

	public function arrayify() {
		$array = [
			'level' => $this->level;
			'code' => $this->code;
			'name' => $this->name;
			'message' => $this->message;
			'timestamp' => $this->timestamp;
		];

		return $array;
	}

	public function jsonify() {
		$values = json_encode($this->arrayify());
	}
}
?>
