<?php
spl_autoload_register(function($name){
	if(substr($name, 0, 11) == 'Astronauth\\'){
		$file = __DIR__ . DIRECTORY_SEPARATOR . str_replace(['Astronauth\\', '\\'], ['', DIRECTORY_SEPARATOR], $name) . '.php';

		if(file_exists($file)){
			require $file;
		}
	}
});
?>
