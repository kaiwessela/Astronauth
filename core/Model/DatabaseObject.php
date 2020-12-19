<?php
namespace Astronauth\Model;
use \Astronauth\Config\Config;
use \Astronauth\Exceptions\WrongObjectStateException;
use \PDO;

abstract class DatabaseObject {
	public $id;

	protected $new;
	protected $empty;


	function __construct() {
		$this->new = false;
		$this->empty = true;
	}

	public function generate() {
		if(!$this->is_empty()){
			throw new WrongObjectStateException('empty');
		}

		$this->new = true;
		$this->empty = false;

		$this->generate_id();
	}

	public static function open_pdo() {
		return new PDO(
			'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME,
			Config::DB_USER,
			Config::DB_PASSWORD,
			[PDO::ATTR_PERSISTENT => true]
		);
	}

	public function is_new() {
		return (bool) $this->new;
	}

	public function is_empty() {
		return (bool) $this->empty;
	}

	protected function generate_id() {
		$this->id = bin2hex(openssl_random_pseudo_bytes(16));
	}
}
?>
