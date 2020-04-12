<?php

function pre($param)
{
	echo '<pre>';
	var_dump($param);
	echo '</pre>';
	exit;
}

$Route;
function route($name = '', $args = array()){
	global $Route;
	if(empty($name)){
		$url = [];
		$url['current'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$url['root'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
		return $url;
	} else {
		return $Route->route($name, $args);
	}
}

function view(string $View, array $args = array())
{
	//get the args
	foreach ($args as $arg => $value) {
		$$arg = $value;
	}
	$separator = array('\\', '/');
	$View = str_replace($separator, DIRECTORY_SEPARATOR, "../" . $View);
	ob_start();
	//verify if file exists
	if (file_exists($View . '.php')) {
		include_once($View . '.php');
	} else {
		throw new \Exception("View '$View' not found");
	}
	ob_end_flush();
}
