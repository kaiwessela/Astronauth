<?php
namespace Astronauth\Backend;
use Astronauth\Config\Config;
use PDO;

trait ModelTrait {
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
}
?>
