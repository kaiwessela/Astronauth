<?php
session_start();

spl_autoload_register(function($name){
	$file = DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $name) . '.php';
	$include = __DIR__ . strtolower(str_replace(DIRECTORY_SEPARATOR . 'Astronauth', '', $file));

	if(file_exists($include)){
		require_once $include;
	}
});

$endpoint = new \Astronauth\Frontend\Web\Endpoint();
$endpoint->handle();
?>
