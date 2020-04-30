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

/**
 * Return a public path folder from project
 */
function assets(){
	return route()['root'];
}

/**
 * Generate a Slug
 */
function slugfy($str) {
    $search = array('Ș', 'Ț', 'ş', 'ţ', 'Ş', 'Ţ', 'ș', 'ț', 'î', 'â', 'ă', 'Î', 'Â', 'Ă', 'ë', 'Ë', 'á');
    $replace = array('s', 't', 's', 't', 's', 't', 's', 't', 'i', 'a', 'a', 'i', 'a', 'a', 'e', 'E', 'a');
    $str = str_ireplace($search, $replace, strtolower(trim($str)));
    $str = preg_replace('/[^\w\d\-\ ]/', '', $str);
    $str = str_replace(' ', '-', $str);
    return preg_replace('/\-{2,}/', '-', $str);
}
?>