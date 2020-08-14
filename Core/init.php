<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/Sao_Paulo');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

/**
 * Show a message if there error
 */
function ErrorHandler($error_level, $error_message, $error_file, $error_line)
{
	header('HTTP/1.1 500 Internal Server Error');
	ob_get_clean();
	echo '<div style="background: #f1f1f1;padding: 20px;">';
	echo '<h3 style="margin-left:30px">Ops there a Error</h3>';
	echo '<b style="margin-left:30px">Error:</b> ' . $error_message . '<br>';
	echo '<b style="margin-left:30px">Level:</b> ' .  $error_level . '<br>';
	echo '<b style="margin-left:30px">File:</b> ' .  $error_file . '<br>';
	echo '<b style="margin-left:30px">Line:</b> ' .  $error_line . '<br>';
	echo '<b style="margin-left:30px">PHP:</b> ' .  PHP_VERSION  . ' (' . PHP_OS . ')<br>';
	echo '</div>';
	die();
}

/**
 * Show a exception message
 */
function ExceptionHandler($e)
{
	header('HTTP/1.1 500 Internal Server Error');
	ob_get_clean();
	echo '<div style="background: #f1f1f1;padding: 20px;">';
	echo '<h3 style="margin-left:30px">Ops there an exception</h3>';
	echo '<b style="margin-left:30px">Exception:</b> ' . $e->getMessage() . '<br>';
	echo '<b style="margin-left:30px">File:</b> ' . $e->getFile() . '<br>';
	echo '<b style="margin-left:30px">Line:</b> ' . $e->getLine() . '<br>';
	echo '<b style="margin-left:30px">PHP:</b> ' .  PHP_VERSION  . ' (' . PHP_OS . ')<br>';
	echo '</div>';
	die();
}
set_error_handler('ErrorHandler');
set_exception_handler('ExceptionHandler');
require_once('../autoload.php');
include_once("../Config.php");
include_once('helpers.php');

use Core\Core;

new Core;
