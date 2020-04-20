<?php
$Route;

/**
 * Get a specific route
 */
function route($name = '', $fullURL = false,$args = array()){
	global $Route;

	if(empty($name)){
		$url = [];
		$url['current'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$url['root'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
		return $url;
	} else {
		return $Route->route($name, $fullURL ,$args);
	}
}

function assets(){
	return route()['root'];
}
?>