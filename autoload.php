<?php

/**
 * Register de autoload
 */
spl_autoload_register(function ($class) {
	/*List of separators */
	$separator = array('\\', '/');

	/*change the separators*/
	$file = str_replace($separator, DIRECTORY_SEPARATOR, $class);
	/*verify if file exist*/
	if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . $file . ".php")) {
		require_once __DIR__ . DIRECTORY_SEPARATOR . $file . '.php';
	} else {
		throw new Exception("File not found '$class'", 1);
	}
});
