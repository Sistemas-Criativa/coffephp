<?php

use Core\Request;

/**
 * Return a view
 */
function view(string $View, array $args = array())
{
	ob_start();
	//verify if file exists

	including($View, $args);

	Request::clearDataSession('flashed');
	return ob_get_contents();
	ob_end_flush();
}
