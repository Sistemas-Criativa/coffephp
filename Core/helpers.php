<?php

include_once('Helpers/route.php');
include_once('Helpers/config.php');
include_once('Helpers/session.php');
include_once('Helpers/redirect.php');
include_once('Helpers/token.php');
include_once('Helpers/view.php');
include_once('Helpers/including.php');
include_once('Helpers/request.php');
include_once('Helpers/response.php');

function pre($param)
{
	echo '<pre>';
	var_dump($param);
	echo '</pre>';
	exit;
}
