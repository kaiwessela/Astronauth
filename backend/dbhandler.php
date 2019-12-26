<?php
class DatabaseHandler {
	private $pdo;

	public function connect($host, $dbname, $username, $password) {
		$this->pdo = new PDO('mysql:host=' . $host . ';dbname=' . $dbname, $username, $password);
	}



	public function disconnect() {
		$this->pdo = null;
	}
}

class DatabaseRequest {
	private $query;
	private $values;
	private $options;


	function __construct($query, $values = array(), $options = null) {
		if(is_string($query) && $query != ''){
			$this->query = $query;
		} else {
			$this->query = null;
		}

		if(is_array($values)){
			$this->values = $values;
		} else {
			$this->values = null;
		}

		if(isset($options){
			if(is_array($options)){
				$this->options = $options;
			} else {
				$this->options = null;
			}
		}
	}

	public function setQuery($query) {
		if(is_string($query) && $query != ''){
			$this->query = $query;
		} else {
			$this->query = null;
		}
	}

	public function setValues($values) {
		if(is_array($values)){
			$this->values = $values;
		} else {
			$this->values = null;
		}
	}

	public function setOptions($options) {
		if(isset($options){
			if(is_array($options)){
				$this->options = $options;
			} else {
				$this->options = null;
			}
		}
	}
}

class DatabaseResponse {

}
?>
