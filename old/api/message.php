<?php
class MessageHandler {
	private $messages = array();

	public function add($name) {
		$this->messages[] = new Message(Tools::uuid(), $name);
	}

	public function get() {
		return $this->messages;
	}

	public function import($messageHandler) {
		$this->messages = array_merge($this->messages, $messageHandler->get());
	}

	public function clear() {
		$this->messages = array();
	}
}

class Message {
	public $uuid;
	public $name;

	function __construct($uuid, $name) {
		$this->uuid = $uuid;
		$this->name = $name;
		return true;
	}
}
?>
