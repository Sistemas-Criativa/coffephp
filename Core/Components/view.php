<?php
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
?>