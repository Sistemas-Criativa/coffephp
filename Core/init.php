<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	date_default_timezone_set("America/Sao_Paulo");
	mb_internal_encoding('UTF-8');
	mb_http_output('UTF-8');

	/**
	 * Show a message if has error
	 */
	function ErrorHandler($error_level, $error_message, $error_file, $error_line) {
		echo "Ops has a Error<br>";
		echo "<b>Error:</b> $error_message<br>";
		echo "<b>Level:</b> $error_level<br>";
		echo "<b>File:</b> $error_file<br>";
		echo "<b>Line:</b> $error_line<br>";
		die();
	}

	/**
	 * Show a exception message
	 */
	function ExceptionHandler($e) {
		echo "Ops has an exception<br>";
		echo "<b>Exception:</b> ". $e->getMessage()."<br>";
		echo "<b>File:</b> ".$e->getFile()."<br>";
		echo "<b>Line:</b> ".$e->getLine()."<br>";
		die();
	}
	set_error_handler("ErrorHandler");
	set_exception_handler('ExceptionHandler'); 
	require_once("autoload.php");

	/**
	 * debug a variable in a pre element
	 */
	function pre($param){
		echo '<pre>';
		var_dump($param);
		echo '</pre>';
		exit;
	}
	use Core\Core;
	new Core;
?>