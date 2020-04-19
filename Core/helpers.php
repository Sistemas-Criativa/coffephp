<?php

include_once('Components/route.php');
include_once('Components/config.php');
include_once('Components/session.php');
include_once('Components/redirect.php');
include_once('Components/token.php');
include_once('Components/view.php');

function pre($param)
{
	echo '<pre>';
	var_dump($param);
	echo '</pre>';
	exit;
}
