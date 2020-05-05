<?php
/**
 * Include an view
 */
function including(string $file, array $args = array()){
    //get the args
	foreach ($args as $arg => $value) {
        global $defined;
        $defined[$arg] = $value;
	}
    $separator = array('\\', '/');
    foreach ($GLOBALS['defined'] as $arg => $value) {
        $$arg = $value;
        unset($GLOBALS[$arg]);
	}
	/*change the separators*/
    $file = str_replace($separator, DIRECTORY_SEPARATOR, $file);
    $view = ROOT . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . $file . ".php";
	if (file_exists($view)) {
		include_once($view);
	} else {
		throw new \Exception("View '$View' not found");
	}
}