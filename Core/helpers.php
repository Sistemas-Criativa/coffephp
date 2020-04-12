<?php

function pre($param)
{
	echo '<pre>';
	var_dump($param);
	echo '</pre>';
	exit;
}

$Route;
function route($name, $args = array()){
	global $Route;
	return $Route->route($name, $args);
}

function view(string $View, array $args = array())
{
	//get the args
	foreach ($args as $arg => $value) {
		$$arg = $value;
	}
	$separator = array('\\', '/');
	$View = str_replace($separator, DIRECTORY_SEPARATOR, "../" . $View);

	//verify if file exists
	if (file_exists($View . '.php')) {
		include_once($View . '.php');
	} else {
		throw new \Exception("View '$View' not found");
	}
}
